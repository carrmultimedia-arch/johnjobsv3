<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/CompanyService.php';

class CompaniesController extends BaseController {
    public function __construct() {
        $service = new CompanyService();
        parent::__construct($service);
    }

    public function stats() {
        $data = $this->service->getStats();
        Response::json(Response::success($data));
    }

    public function bulkStatus() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['ids']) || !isset($data['status'])) {
            Response::json(Response::error('Invalid input: ids and status required'));
        }

        $result = $this->service->bulkUpdateStatus($data['ids'], $data['status']);
        if (!$result) {
            Response::json(Response::error('Failed to update company statuses'));
        }

        Response::json(Response::success(['updated' => true]));
    }

    public function jobs($companyId) {
        $items = $this->service->getJobs($companyId);
        Response::json(Response::success($items, true));
    }
}
