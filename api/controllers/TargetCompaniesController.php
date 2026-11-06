<?php

class TargetCompaniesController extends BaseController {
    public function __construct() {
        $service = new TargetCompanyService();
        parent::__construct($service);
    }
}
