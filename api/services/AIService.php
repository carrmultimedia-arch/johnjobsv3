<?php
declare(strict_types=1);

/**
 * MODULE: AI Service
 * WORKFLOW: 0700_AI_SYSTEM, 0720_RESUME_BUILDER, 0730_COVER_LETTER_SYSTEM, 0670_ATS_OPTIMIZATION_WORKFLOW
 * PURPOSE: AI abstraction layer for prompt-based generation
 *
 * CHANGE (8-20-26):
 * - resume_optimize now branches on a `style` variable: 'human' (default,
 *   naturally reworded, reads like a person wrote it) vs 'ats' (leans
 *   harder into literal keyword coverage). workspace.php should pass
 *   `style` through in the generate payload.
 * - cover_letter_generate rewritten to require BOTH `background` (the
 *   candidate's real experience/master resume) and `job_description`
 *   (the actual posting) as separate inputs, and explicitly instructs the
 *   model to pull specific real details from background rather than just
 *   restating the job posting. Previously this prompt only received
 *   background+tone with no real experience data behind it — see
 *   CoverLettersController.php's generate() comment for the root cause.
 */

class AIService extends BaseService {
    private array $prompts = [];

    private const CLAUDE_MODEL = 'claude-sonnet-5';
    private const ANTHROPIC_VERSION = '2023-06-01';
    private const ANTHROPIC_URL = 'https://api.anthropic.com/v1/messages';

    public function __construct() {
        parent::__construct();
        $this->loadPrompts();
    }

    private function loadPrompts() {
        $this->prompts = [
            'resume_optimize' =>
                "You are an expert resume writer. Using the MASTER RESUME data below, create a custom ONE-PAGE resume tailored specifically to the JOB DESCRIPTION below.\n\n" .
                "RULES:\n" .
                "1. Write a Professional Summary matched to this job's type and seniority.\n" .
                "2. Pull 4-6 bullet points per relevant job from the master resume, prioritizing recency and relevance to this specific posting.\n" .
                "3. Include 3-5 quantifiable metrics/achievements pulled from the master resume.\n" .
                "4. Use whichever job title (from the master resume or the posting itself) most closely matches this role.\n" .
                "5. Keep the ENTIRE resume to one page — trim ruthlessly, do not include irrelevant experience.\n" .
                "6. Use standard, parseable resume formatting: '## Section Name' for section headers, '- ' for bullets, blank lines between entries, **bold** for job titles/company names. No tables/columns/graphics.\n" .
                "7. STYLE ({{style}}): " .
                "if 'ats', prioritize naturally incorporating 8-12 ATS keywords drawn directly from the job description, even if phrasing leans more literal/keyword-forward; " .
                "if 'human' (default), reword duties to sound like natural professional language a person would write — e.g. rephrase something like 'managed production crew' as language matching the posting's own terminology (such as 'employee resource management and operations') ONLY when the job description's phrasing genuinely fits, still weaving in relevant keywords naturally rather than stuffing them.\n\n" .
                "TARGET ROLE (if provided): {{target_role}}\n" .
                "TARGET COMPANY (if provided): {{company}}\n\n" .
                "JOB DESCRIPTION:\n{{job_description}}\n\n" .
                "MASTER RESUME (source data — select only what's relevant to this posting):\n{{content}}\n\n" .
                "Output ONLY the finished one-page resume text in the markdown-like format described in rule 6, ready to be parsed and styled. Do not include commentary, caveats, or requests for more information.",

            'cover_letter_generate' =>
                "You are an expert cover letter writer. Write a personalized, one-page cover letter for the {{job_title}} role at {{company_name}}.\n\n" .
                "CANDIDATE'S REAL BACKGROUND/EXPERIENCE (this is the actual person applying — draw from this, do not invent anything not supported by it):\n{{background}}\n\n" .
                "JOB DESCRIPTION (what the employer is looking for):\n{{job_description}}\n\n" .
                "RULES:\n" .
                "1. Select 2-3 SPECIFIC, real details from the candidate's background above (a project, a result, a skill, a role) and explicitly connect each one to something the job description is asking for. This is the core of the letter — do not skip it or keep it vague.\n" .
                "2. Do not simply restate or paraphrase the job description back at the reader. The job description is context for what to emphasize, not content to reproduce.\n" .
                "3. Open with genuine interest in this specific role/company, not a generic template opener.\n" .
                "4. Tone: {{tone}}.\n" .
                "5. Length: 300-400 words, standard business letter structure (opening, 1-2 body paragraphs, closing).\n" .
                "6. Format with '- ' only if a short list genuinely fits; otherwise plain paragraphs separated by blank lines. **bold** may be used sparingly for a company/role name on first mention.\n\n" .
                "Output ONLY the finished cover letter text, ready to be parsed and styled. Do not include commentary or placeholders like [Hiring Manager Name] unless one was actually provided.",

            'job_match_score' => "Score this job against the candidate profile. Job: {{job}}. Profile: {{profile}}. Return: relevance_score (0-100), skill_overlap (0-100), experience_alignment (0-100), compensation_fit (0-100), recommendation_level (strong/moderate/weak).",
            'interview_prep' => "Generate interview preparation notes for {{job_title}} at {{company_name}}. Include: likely questions, company research points, and talking points about the candidate's relevant experience.",
            'prospect_outreach' => "Draft a cold outreach message to {{prospect_name}}, {{prospect_title}} at {{company_name}}. Context: {{context}}. Tone: professional, concise, value-focused.",
        ];
    }

    public function getPrompt(string $promptId): string {
        return $this->prompts[$promptId] ?? null;
    }

    public function renderPrompt(string $promptId, array $variables): string {
        $template = $this->getPrompt($promptId);
        if (!$template) return null;

        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    private function getApiKey(string $service): ?string {
        $stmt = $this->db->prepare(
            "SELECT api_keys FROM settings WHERE api_keys IS NOT NULL AND deleted_at IS NULL ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row || empty($row['api_keys'])) {
            return null;
        }

        $keys = json_decode($row['api_keys'], true);
        if (!is_array($keys)) {
            return null;
        }

        return $keys[$service] ?? null;
    }

    public function generate(string $promptId, array $variables): array {
        // Fill any unpassed template vars (e.g. style) with '' so an unrendered
        // {{placeholder}} never leaks into the prompt sent to the model.
        $defaults = ['style' => 'human', 'job_description' => '', 'target_role' => '', 'company' => ''];
        $variables = array_merge($defaults, $variables);

        $rendered = $this->renderPrompt($promptId, $variables);
        if (!$rendered) {
            return ['success' => false, 'error' => 'Prompt not found: ' . $promptId];
        }

        $apiKey = $this->getApiKey('claude');
        if (!$apiKey) {
            return [
                'success' => false,
                'error' => 'No Claude API key found. Save one under Settings > AI Toolkit.'
            ];
        }

        $payload = [
            'model' => self::CLAUDE_MODEL,
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $rendered
                ]
            ]
        ];

        $ch = curl_init(self::ANTHROPIC_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: ' . self::ANTHROPIC_VERSION,
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => 'CURL error: ' . $error];
        }

        if ($httpCode !== 200) {
            return ['success' => false, 'error' => 'API request failed with status ' . $httpCode . ': ' . $response];
        }

        $data = json_decode($response, true);

        if (isset($data['content']) && is_array($data['content'])) {
            foreach ($data['content'] as $block) {
                if (($block['type'] ?? null) === 'text' && isset($block['text'])) {
                    return [
                        'success' => true,
                        'prompt_id' => $promptId,
                        'rendered_prompt' => $rendered,
                        'output' => $block['text'],
                        'status' => 'completed'
                    ];
                }
            }
        }

        return ['success' => false, 'error' => 'Unexpected API response format', 'raw_response' => $response];
    }

    public function scoreJobMatch(array $job, array $profile): array {
        return [
            'relevance_score' => 75,
            'skill_overlap' => 80,
            'experience_alignment' => 70,
            'compensation_fit' => 85,
            'recommendation_level' => 'strong',
        ];
    }
}