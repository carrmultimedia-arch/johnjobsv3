<?php
declare(strict_types=1);

/**
 * MODULE: Logging Middleware
 * WORKFLOW: All
 * PURPOSE: Request/response logging per 0810
 */

class LoggingMiddleware {
    private static float $startTime;

    public static function start() {
        self::$startTime = microtime(true);
    }

    public static function log(string $method, string $endpoint, bool $success) {
        $duration = round((microtime(true) - self::$startTime) * 1000, 2);
        $status = $success ? 'SUCCESS' : 'FAILURE';
        Logger::api($method, $endpoint, $status, $duration);
    }
}
