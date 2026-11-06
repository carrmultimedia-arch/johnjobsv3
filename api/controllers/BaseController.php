<?php

class BaseController {
    protected $service;
    
    public function __construct($service) {
        $this->service = $service;
    }
    
    public function list() {
        $items = $this->service->getAll();
        Response::json(Response::success($items, true));
    }
    
    public function get($id) {
        $item = $this->service->getById($id);
        if (!$item) {
            Response::json(Response::error('Item not found'));
        }
        Response::json(Response::success($item));
    }
    
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(Response::error('Invalid JSON input'));
        }
        $result = $this->service->create($data);
        if (!$result) {
            Response::json(Response::error('Failed to create item'));
        }
        Response::json(Response::success($result));
    }
    
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(Response::error('Invalid JSON input'));
        }
        $result = $this->service->update($id, $data);
        if (!$result) {
            Response::json(Response::error('Failed to update item'));
        }
        Response::json(Response::success($result));
    }
    
    public function delete($id) {
        $result = $this->service->delete($id);
        if (!$result) {
            Response::json(Response::error('Failed to delete item'));
        }
        Response::json(Response::success(['deleted' => true]));
    }
}
