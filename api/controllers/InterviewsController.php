<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/InterviewService.php';

class InterviewsController extends BaseController {
    public function __construct() {
        $service = new InterviewService();
        parent::__construct($service);
    }
    
    public function byApplication($applicationId) {
        $items = $this->service->getByApplicationId($applicationId);
        Response::json(Response::success($items, true));
    }

    public function upcoming() {
        $items = $this->service->getUpcoming();
        Response::json(Response::success($items, true));
    }

    public function calendar() {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $items = $this->service->getByScheduledRange($start, $end);
        Response::json(Response::success($items, true));
    }
}
