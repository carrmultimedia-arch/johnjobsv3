<?php

class ApplicationsController extends BaseController {
    public function __construct() {
        $service = new ApplicationService();
        parent::__construct($service);
    }

    public function pipeline() {
        $items = $this->service->getPipeline();
        Response::json(Response::success($items, true));
    }

    public function stage() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id']) || !isset($data['status'])) {
            Response::json(Response::error('Invalid input: id and status required'));
        }

        $result = $this->service->setStatus($data['id'], $data['status']);
        if (!$result) {
            Response::json(Response::error('Failed to update application stage'));
        }

        Response::json(Response::success($this->service->getById($data['id'])));
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
        http_response_code(200); // force 200 so the real message reaches the browser instead of a generic 500
        Response::json(Response::error('DEBUG: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()));
    }
}
}
