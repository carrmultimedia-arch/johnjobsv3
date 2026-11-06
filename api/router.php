<?php

require_once __DIR__ . '/bootstrap.php';

use api\controllers\ApplicationsController;
use api\controllers\ProspectsController;
use api\controllers\JobsController;
use api\controllers\CompaniesController;
use api\controllers\ResumesController;
use api\controllers\CoverLettersController;
use api\controllers\DocumentsController;
use api\controllers\ConversationsController;
use api\controllers\InterviewsController;
use api\controllers\SavedSearchesController;
use api\controllers\TargetCompaniesController;
use api\controllers\SettingsController;
use api\controllers\ExportController;

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($uri, PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));

// Expect /api/{resource}/{id}
if (count($parts) < 2 || $parts[0] !== 'api') {
    Response::sendError('Invalid API route', 404);
    exit;
}

$resource = $parts[1] ?? null;
$id = $parts[2] ?? null;
$action = ($id && isset($parts[3])) ? $parts[3] : null;

$controller = null;
$data = json_decode(file_get_contents('php://input'), true);

switch ($resource) {
    case 'applications':
        $controller = new ApplicationsController();
        if ($method === 'POST' && $id === 'batch-update-sort') {
            $controller->batchUpdateSort($data);
            exit;
        }
        break;
    case 'prospects':
        $controller = new ProspectsController();
        if ($method === 'POST' && $id === 'batch-update-sort') {
            $controller->batchUpdateSort($data);
            exit;
        }
        break;
    case 'jobs':
        $controller = new JobsController();
        if ($method === 'POST' && $id === 'batch-update-sort') {
            $controller->batchUpdateSort($data);
            exit;
        }
        break;
    case 'companies':
        $controller = new CompaniesController();
        break;
    case 'resumes':
        $controller = new ResumesController();
        break;
    // ... other resources
    default:
        Response::sendError("Resource '{$resource}' not found.", 404);
        exit;
}

if (!$controller) {
    Response::sendError("Controller for '{$resource}' not found.", 404);
    exit;
}

switch ($method) {
    case 'GET':
        if ($id) {
            $controller->get($id);
        } else {
            $controller->list();
        }
        break;
    case 'POST':
        $controller->create($data);
        break;
    case 'PUT':
        if ($id) {
            $controller->update($id, $data);
        }
        break;
    case 'DELETE':
        if ($id) {
            $controller->delete($id);
        }
        break;
    default:
        Response::sendError("Method '{$method}' not supported.", 405);
        break;
}