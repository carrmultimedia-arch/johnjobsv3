<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/JobService.php';

class JobsController extends BaseController {
    public function __construct() {
        $service = new JobService();
        parent::__construct($service);
    }
    
    public function batchUpdateSort() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['updates']) || !is_array($data['updates'])) {
            Response::json(Response::error('Invalid input: updates array required'));
        }

        $result = $this->service->batchUpdateSort($data['updates']);
        if (!$result) {
            Response::json(Response::error('Failed to update sort order'));
        }

        Response::json(Response::success(['updated' => count($data['updates'])]));
    }
    
    public function byCompany($companyId) {
        $items = $this->service->getByCompanyId($companyId);
        Response::json(Response::success($items, true));
    }

    public function stats() {
        if (method_exists($this->service, 'getStats')) {
            $data = $this->service->getStats();
            Response::json(Response::success($data));
        }
        Response::json(Response::error('Job statistics unavailable'));
    }
}
