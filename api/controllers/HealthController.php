<?php
/**
 * JobHunt Command Center - Health Controller
 * johnjobsv2/api/controllers/HealthController.php
 * 
 * Returns system health status. No auth required.
 */

require_once __DIR__ . '/BaseController.php';

class HealthController extends BaseController {

    public function handle(): void {
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'php_version' => PHP_VERSION,
            'api_version' => defined('API_VERSION') ? API_VERSION : '1.0',
            'database' => $this->checkDatabase(),
            'server' => [
                'name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            ]
        ];

        $this->success($health, 'System healthy');
    }

    private function checkDatabase(): array {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()");
            $tables = $stmt->fetchAll();
            $tableNames = array_column($tables, 'table_name');

            return [
                'connected' => true,
                'driver' => 'mysql',
                'tables' => $tableNames,
                'table_count' => count($tableNames)
            ];
        } catch (Throwable $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
