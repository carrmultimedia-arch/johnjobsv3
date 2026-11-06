<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/UserService.php';

class UsersController extends BaseController {
    public function __construct() {
        $service = new UserService();
        parent::__construct($service);
    }
    
    public function byEmail($email) {
        $item = $this->service->getByEmail($email);
        if (!$item) {
            Response::json(Response::error('User not found'));
        }
        Response::json(Response::success($item));
    }
}
