<?php

class DocumentsController extends BaseController {
    public function __construct() {
        $service = new DocumentService();
        parent::__construct($service);
    }

    public function type() {
        $type = $_GET['type'] ?? null;
        if (!$type) {
            Response::json(Response::error('Document type required'));
        }
        $items = $this->service->getByType($type);
        Response::json(Response::success($items, true));
    }

    public function active() {
        $items = $this->service->getActiveDocuments();
        Response::json(Response::success($items, true));
    }
}
