<?php

class SettingsController extends BaseController {
    public function __construct() {
        $service = new SettingsService();
        parent::__construct($service);
    }
    
    public function delete($id) {
        Response::json(Response::error('Cannot delete settings'));
    }
}
