<?php
require_once __DIR__ . '/../utils/Response.php';

class AdminController {
    public function list(): void {
        Response::json(Response::success([
            'message' => 'AdminController ready',
            'status' => 'ok'
        ]));
    }

    public function systemHealth(): void {
        try {
            $db = DB::getInstance();
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $health = [
                'status' => 'ok',
                'timestamp' => date('c'),
                'php_version' => PHP_VERSION,
                'database' => [
                    'connected' => true,
                    'tables' => $tables,
                    'table_count' => count($tables)
                ]
            ];
            Response::json(Response::success($health));
        } catch (Throwable $e) {
            Response::json(Response::error('Health check failed: ' . $e->getMessage()));
        }
    }

    public function tables(): void {
        try {
            $db = DB::getInstance();
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            Response::json(Response::success($tables, true));
        } catch (Throwable $e) {
            Response::json(Response::error('Failed to fetch tables: ' . $e->getMessage()));
        }
    }

    public function logs(): void {
        $lines = isset($_GET['lines']) ? max(1, (int)$_GET['lines']) : 50;
        $logFile = LOGS_PATH . 'app.log';

        if (!file_exists($logFile)) {
            Response::json(Response::error('Log file not found'));
        }

        $content = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $tail = array_slice($content, -$lines);
        Response::json(Response::success([
            'lines' => array_values($tail),
            'total' => count($content)
        ]));
    }

    public function clearLogs(): void {
        $logFile = LOGS_PATH . 'app.log';
        if (!file_exists($logFile)) {
            Response::json(Response::error('Log file not found'));
        }
        file_put_contents($logFile, '');
        Response::json(Response::success(['cleared' => true]));
    }

    public function backup(): void {
        try {
            $db = DB::getInstance();
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $backupDir = ROOT_PATH . '/database/backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $filename = $backupDir . '/backup_' . date('Ymd_His') . '.sql';
            $sql = '';
            foreach ($tables as $table) {
                $row = $db->query("SHOW CREATE TABLE `{$table}`")->fetch();
                if ($row && isset($row['Create Table'])) {
                    $sql .= $row['Create Table'] . ";\n\n";
                }
            }
            file_put_contents($filename, $sql);
            $relativePath = str_replace([ROOT_PATH . '/', ROOT_PATH . '\\'], '', $filename);
            Response::json(Response::success(['backup_path' => $relativePath]));
        } catch (Throwable $e) {
            Response::json(Response::error('Backup failed: ' . $e->getMessage()));
        }
    }
}