<?php
declare(strict_types=1);

/**
 * MODULE: Logger
 * WORKFLOW: All
 * PURPOSE: System logging utility
 */

class Logger {
    private static function write(string $level, string $message) {
        $logFile = LOGS_PATH . 'app.log';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] [$level] $message" . PHP_EOL;
        error_log($entry, 3, $logFile);
    }

    public static function debug(string $message) {
        if (LOG_LEVEL === 'debug') self::write('DEBUG', $message);
    }
    public static function info(string $message) { self::write('INFO', $message); }
    public static function warning(string $message) { self::write('WARNING', $message); }
    public static function error(string $message) { self::write('ERROR', $message); }
    public static function api(string $method, string $endpoint, string $status, float $duration = null) {
        $msg = "$method $endpoint → $status";
        if ($duration) $msg .= " (${duration}ms)";
        self::write('API', $msg);
    }
}
