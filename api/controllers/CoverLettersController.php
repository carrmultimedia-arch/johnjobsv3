<?php

require_once ROOT_PATH . '/api/services/AIService.php';
require_once ROOT_PATH . '/lib/DocumentFormatter.php'; // ADJUST PATH if you place this file elsewhere — see WORK_ORDER.md

class CoverLettersController extends BaseController {
    private $aiService;

    public function __construct() {
        $service = new CoverLetterService();
        parent::__construct($service);
        $this->aiService = new AIService();
    }

    public function generate() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(Response::error('Invalid JSON input'));
            return;
        }

        $jobTitle = $data['job_title'] ?? '';
        $companyName = $data['company_name'] ?? '';
        // FIXED: 'background' must be the candidate's real experience, not
        // the job description. workspace.php was previously sending the
        // job-description textarea's value as background — that's the bug
        // that made every cover letter just paraphrase the job posting.
        $background = $data['background'] ?? '';
        $jobDescription = $data['job_description'] ?? '';
        $hiringManager = $data['hiring_manager'] ?? '';
        $tone = $data['tone'] ?? 'professional';

        if (empty($jobTitle) || empty($companyName) || empty($background)) {
            Response::json(Response::error('job_title, company_name, and background (candidate experience) are required'));
            return;
        }

        $aiResult = $this->aiService->generate('cover_letter_generate', [
            'job_title' => $jobTitle,
            'company_name' => $companyName,
            'background' => $background,
            'job_description' => $jobDescription,
            'tone' => $tone
        ]);

        if (!$aiResult['success']) {
            Response::json(Response::error($aiResult['error']));
            return;
        }

        $coverLetterData = [
            'title' => $jobTitle,
            'company' => $companyName,
            'content' => $aiResult['output'] ?? $aiResult['rendered_prompt'],
            'hiring_manager' => $hiringManager,
            'tone' => $tone
        ];
        if (isset($data['application_id'])) {
            $coverLetterData['application_id'] = $data['application_id'];
        }

        $result = $this->service->create($coverLetterData);
        if (!$result) {
            Response::json(Response::error('Failed to save generated cover letter'));
            return;
        }

        Response::json(Response::success($result));
    }

    public function job() {
        $jobId = $_GET['job_id'] ?? null;
        if (!$jobId) {
            Response::json(Response::error('Job id required'));
        }
        $items = $this->service->getByJobId($jobId);
        Response::json(Response::success($items, true));
    }

    /** NEW: GET /api/cover-letters/:id/html — see ResumesController::html() for logic notes. */
    public function html($id) {
        $letter = $this->service->getById($id);
        if (!$letter) {
            Response::json(Response::error('Cover letter not found'));
            return;
        }

        $content = $letter['content'] ?? '';
        $metadata = $this->decodeMetadata($letter['metadata'] ?? null);
        $editedHtml = $metadata['edited_html'] ?? null;
        $html = ($editedHtml !== null && $editedHtml !== '')
            ? $editedHtml
            : DocumentFormatter::toHtml($content);

        Response::json(Response::success([
            'html' => $html,
            'title' => $letter['title'] ?? 'Cover Letter',
            'is_edited' => !empty($editedHtml)
        ]));
    }

    /** NEW: PUT /api/cover-letters/:id/html — stores edits inside the existing metadata JSON column. */
    public function saveHtml($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['html'])) {
            Response::json(Response::error('html field required'));
            return;
        }

        $letter = $this->service->getById($id);
        if (!$letter) {
            Response::json(Response::error('Cover letter not found'));
            return;
        }

        $metadata = $this->decodeMetadata($letter['metadata'] ?? null);
        $metadata['edited_html'] = $data['html'];

        // NOTE: CoverLetterService::update() expects raw 'metadata' as an array.
        $result = $this->service->update($id, ['metadata' => $metadata]);
        if (!$result) {
            Response::json(Response::error('Failed to save edits'));
            return;
        }

        Response::json(Response::success($result));
    }

    /** metadata comes back from CoverLetterService as a JSON string; decode safely. */
    private function decodeMetadata($raw): array {
        if (is_array($raw)) return $raw;
        if (empty($raw)) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function pdf($id) {
        $letter = $this->service->getById($id);
        if (!$letter) {
            Response::json(Response::error('Cover letter not found'));
            return;
        }

        $content = $letter['content'] ?? '';
        $title = $letter['title'] ?? 'Cover Letter';
        $metadata = $this->decodeMetadata($letter['metadata'] ?? null);
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