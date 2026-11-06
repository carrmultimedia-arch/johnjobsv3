<?php

class SavedSearchesController extends BaseController {
    public function __construct() {
        $service = new SavedSearchService();
        parent::__construct($service);
    }
}
