<?php

class CalendarEventsController extends BaseController {
    public function __construct() {
        $service = new CalendarEventService();
        parent::__construct($service);
    }
}
