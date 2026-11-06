<?php

require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/AnalyticsService.php';

class AnalyticsController extends BaseController {
    public function __construct() {
        $service = new AnalyticsService();
        parent::__construct($service);
    }
    
    public function funnel() {
        $items = $this->service->getApplicationFunnel();
        Response::json(Response::success($items, true));
    }
    
    public function sources() {
        $items = $this->service->getSourceBreakdown();
        Response::json(Response::success($items, true));
    }
    
    public function timing() {
        $items = $this->service->getTimeToResponse();
        Response::json(Response::success($items, true));
    }
    
    public function conversions() {
        $data = $this->service->getConversionRates();
        Response::json(Response::success($data));
    }

    public function summary() {
        $overview = $this->service->getOverview();
        $applications = $overview['applications'] ?? [];

        $followUps = 0;
        if (!empty($overview['conversations']) && is_array($overview['conversations'])) {
            foreach ($overview['conversations'] as $conversationRow) {
                $followUps += $conversationRow['follow_ups_needed'] ?? 0;
            }
        }

        $summary = [
            'applications_total' => $applications['total'] ?? 0,
            'interviews_total' => $applications['interviewing'] ?? 0,
            'offers_total' => $applications['offers'] ?? 0,
            'prospects_total' => $overview['prospects']['total'] ?? 0,
            'conversation_follow_ups' => $followUps,
        ];

        Response::json(Response::success($summary));
    }

    public function dashboard() {
        $counts = $this->service->getEntityCounts();
        $pipeline = $this->service->getApplicationFunnel();
        Response::json(Response::success(['counts' => $counts, 'pipeline' => $pipeline]));
    }

    public function applications() {
        $items = $this->service->getRecentActivity();
        Response::json(Response::success($items, true));
    }

    public function companies() {
        $counts = $this->service->getEntityCounts();
        Response::json(Response::success(['companies' => $counts['companies'] ?? 0]));
    }

    public function activity() {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $items = $this->service->getRecentActivity($start, $end);
        Response::json(Response::success($items, true));
    }
}
