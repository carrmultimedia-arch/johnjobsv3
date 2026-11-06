<?php
require_once __DIR__ . '/BaseController.php';
class ProspectController extends BaseController {
    public function handle(): void {
        $this->success(['message' => 'ProspectController ready', 'method' => $this->method]);
    }
}