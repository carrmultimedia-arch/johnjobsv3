<?php

class ProspectsController extends BaseController {
    public function __construct() {
        $service = new ProspectService();
        parent::__construct($service);
    }
    
    public function batchDiscard() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['ids'])) {
            Response::json(Response::error('Invalid input: ids required'));
        }
        $result = $this->service->batchDiscard($data['ids']);
        if (!$result) {
            Response::json(Response::error('Failed to discard prospects'));
        }
        Response::json(Response::success(['discarded' => true]));
    }
    
    public function review($id) {
        $result = $this->service->review($id);
        if (!$result) {
            Response::json(Response::error('Failed to mark as reviewed'));
        }
        Response::json(Response::success($this->service->getById($id)));
    }

    public function company($companyId) {
        $items = $this->service->getByCompanyId($companyId);
        Response::json(Response::success($items, true));
    }

    public function batchUpdateSort() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['updates']) || !is_array($data['updates'])) {
                Response::json(Response::error('Invalid input: updates array required'));
                return;
            }

            $result = $this->service->batchUpdateSort($data['updates']);
            if (!$result) {
                Response::json(Response::error('Failed to update sort order'));
                return;
            }

            Response::json(Response::success(['updated' => count($data['updates'])]));
        } catch (Throwable $e) {
            http_response_code(200);
            Response::json(Response::error('DEBUG: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()));
        }
    }
}