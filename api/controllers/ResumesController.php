<?php

require_once ROOT_PATH . '/api/services/AIService.php';
require_once ROOT_PATH . '/lib/DocumentFormatter.php'; // ADJUST PATH if you place this file elsewhere — see WORK_ORDER.md

class ResumesController extends BaseController {
    private $aiService;

    public function __construct() {
        $service = new ResumeService();
        parent::__construct($service);
        $this->aiService = new AIService();
    }

    public function generate() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(Response::error('Invalid JSON input'));
            return;
        }

        $masterResume = $data['master_resume'] ?? '';
        $jobDescription = $data['job_description'] ?? '';
        $targetRole = $data['target_role'] ?? '';
        $company = $data['company'] ?? '';
        // 'human' (default) writes naturally and rewords duties to fit the role;
        // 'ats' leans harder into literal keyword coverage for parsers.
        $style = $data['style'] ?? 'human';

        if (empty($masterResume) || empty($jobDescription)) {
            Response::json(Response::error('master_resume and job_description are required'));
            return;
        }

        $aiResult = $this->aiService->generate('resume_optimize', [
            'content' => $masterResume,
            'job_description' => $jobDescription,
            'target_role' => $targetRole,
            'company' => $company,
            'style' => $style
        ]);

        if (!$aiResult['success']) {
            Response::json(Response::error($aiResult['error']));
            return;
        }

        $resumeData = [
            'title' => ($targetRole ? $targetRole . ' - ' : '') . ($company ? $company : 'Optimized Resume'),
            'content' => $aiResult['output'] ?? $aiResult['rendered_prompt'],
            'target_role' => $targetRole,
            'company' => $company,
            'version' => 1
        ];
        if (isset($data['application_id'])) {
            $resumeData['application_id'] = $data['application_id'];
        }

        $result = $this->service->create($resumeData);
        if (!$result) {
            Response::json(Response::error('Failed to save generated resume'));
            return;
        }

        Response::json(Response::success($result));
    }

    public function active() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['id'])) {
                Response::json(Response::error('Resume id required'));
            }
            $result = $this->service->setActiveResume($data['id']);
            if (!$result) {
                Response::json(Response::error('Failed to set active resume'));
            }
            Response::json(Response::success($result));
            return;
        }

        $resume = $this->service->getActiveResume();
        if (!$resume) {
            Response::json(Response::error('No active resume found'));
        }
        Response::json(Response::success($resume));
    }

    /**
     * NEW: GET /api/resumes/:id/html
     * Returns formatted HTML for the on-screen preview — the edited
     * version if one was saved, otherwise freshly formatted from content.
     */
    public function html($id) {
        $resume = $this->service->getById($id);
        if (!$resume) {
            Response::json(Response::error('Resume not found'));
            return;
        }

        $content = $resume['content'] ?? '';
        $metadata = $this->decodeMetadata($resume['metadata'] ?? null);
        $editedHtml = $metadata['edited_html'] ?? null;
        $html = ($editedHtml !== null && $editedHtml !== '')
            ? $editedHtml
            : DocumentFormatter::toHtml($content);

        Response::json(Response::success([
            'html' => $html,
            'title' => $resume['title'] ?? 'Resume',
            'is_edited' => !empty($editedHtml)
        ]));
    }

    /** metadata comes back from ResumeService as a JSON string; decode safely. */
    private function decodeMetadata($raw): array {
        if (is_array($raw)) return $raw;
        if (empty($raw)) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * NEW: PUT /api/resumes/:id/html
     * Saves the user's edited preview HTML separately from the AI-generated
     * `content` column, so a future regenerate never clobbers manual edits
     * and manual edits never corrupt the source content used for re-prompting.
     *
     * ASSUMES ResumeService/BaseService has a generic update($id, array $data)
     * that whitelists/passes through arbitrary columns, same as confirmed
     * working for ApplicationService::update(). If ResumeService doesn't have
     * this, share ResumeService.php/BaseService.php and I'll add it.
     */
    public function saveHtml($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['html'])) {
            Response::json(Response::error('html field required'));
            return;
        }

        $resume = $this->service->getById($id);
        if (!$resume) {
            Response::json(Response::error('Resume not found'));
            return;
        }

        $metadata = $this->decodeMetadata($resume['metadata'] ?? null);
        $metadata['edited_html'] = $data['html'];

        // NOTE: ResumeService::update() expects raw 'metadata' as an array —
        // it json_encode()s it internally. Do not pre-encode here.
        $result = $this->service->update($id, ['metadata' => $metadata]);
        if (!$result) {
            Response::json(Response::error('Failed to save edits'));
            return;
        }

        Response::json(Response::success($result));
    }

    public function pdf($id) {
        $resume = $this->service->getById($id);
        if (!$resume) {
            Response::json(Response::error('Resume not found'));
            return;
        }

        $content = $resume['content'] ?? '';
        $title = $resume['title'] ?? 'Resume';
        $metadata = $this->decodeMetadata($resume['metadata'] ?? null);
        $editedHtml = $metadata['edited_html'] ?? null;

        require_once ROOT_PATH . '/lib/dompdf/vendor/autoload.php';

        $html = DocumentFormatter::toPdfHtml($content, $title, $editedHtml);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title) . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $dompdf->output();
        exit;
    }
}