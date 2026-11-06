<?php

class ConversationsController extends BaseController {
    public function __construct() {
        $service = new ConversationService();
        parent::__construct($service);
    }

    public function type() {
        $type = $_GET['type'] ?? null;
        if (!$type) {
            Response::json(Response::error('Type parameter required'));
        }
        $items = $this->service->getByType($type);
        Response::json(Response::success($items, true));
    }

    public function followUps() {
        $items = $this->service->getFollowUps();
        Response::json(Response::success($items, true));
    }

    public function thread() {
        $threadId = $_GET['thread_id'] ?? null;
        if (!$threadId) {
            Response::json(Response::error('thread_id required'));
        }
        $items = $this->service->getThread($threadId);
        Response::json(Response::success($items, true));
    }
}
