<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/RecruiterService.php';

class RecruitersController extends BaseController {
    public function __construct() {
        $service = new RecruiterService();
        parent::__construct($service);
    }
    
    public function byCompany($companyId) {
        $items = $this->service->getByCompanyId($companyId);
        Response::json(Response::success($items, true));
    }
}
