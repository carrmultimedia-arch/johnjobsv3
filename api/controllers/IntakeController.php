<?php
declare(strict_types=1);

// Define ROOT_PATH directly to avoid circular dependencies with the main bootstrap file.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/api/controllers/BaseController.php';
require_once ROOT_PATH . '/api/services/IntakeService.php';
require_once ROOT_PATH . '/api/utils/Response.php';

class IntakeController extends BaseController {

    public function __construct() {
        $service = new IntakeService();
        parent::__construct($service);
    }

    /**
     * Handles POST /api/intake
     * Creates a new intake record.
     */
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            Response::json(Response::error('Invalid JSON input.'), 400);
            return;
        }

        try {
            $newIntake = $this->service->create($data);
            Response::json(Response::success($newIntake));
        } catch (Exception $e) {
            Response::json(Response::error('Failed to create intake record: ' . $e->getMessage()), 500);
        }
    }

    /**
     * Handles POST /api/intake/{id}/import
     * Imports a job from an intake record.
     */
    public function import($id) {
        if (empty($id)) {
            Response::json(Response::error('Intake ID is required.'), 400);
            return;
        }

        try {
            $result = $this->service->import($id);
            if (!$result) {
                Response::json(Response::error('Failed to import job from intake record ' . $id), 500);
                return;
            }
            Response::json(Response::success(['imported' => true, 'job_id' => $result]));
        } catch (Exception $e) {
            Response::json(Response::error('Failed to import job: ' . $e->getMessage()), 500);
        }
    }
}