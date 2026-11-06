<?php

class LinkedinStatsController extends BaseController {
    public function __construct() {
        $service = new LinkedInStatService();
        parent::__construct($service);
    }
}
