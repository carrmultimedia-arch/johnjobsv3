<?php

class TailoredResumesController extends BaseController {
    public function __construct() {
        $service = new ResumeService();
        parent::__construct($service);
    }
}
