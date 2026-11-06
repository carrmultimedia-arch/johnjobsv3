<?php
declare(strict_types=1);

/**
 * MODULE: Rate Limit Middleware
 * WORKFLOW: All
 * PURPOSE: Abuse prevention per 0810
 */

class RateLimitMiddleware {
    private static array $requests = [];
    private static int $maxRequests = 100;
    private static int $windowSeconds = 60;

    public static function check() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $now = time();

        if (!isset(self::$requests[$ip])) {
            self::$requests[$ip] = [];
        }

        // Clean old requests
        self::$requests[$ip] = array_filter(self::$requests[$ip], fn($t) => $t > $now - self::$windowSeconds);

        if (count(self::$requests[$ip]) >= self::$maxRequests) {
            Response::send(false, null, ['code' => 'SERVER_ERROR', 'message' => 'Rate limit exceeded', 'fields' => []]);
        }

        self::$requests[$ip][] = $now;
    }
}
