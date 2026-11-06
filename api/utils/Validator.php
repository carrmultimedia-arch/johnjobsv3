<?php
declare(strict_types=1);

/**
 * MODULE: Validator
 * WORKFLOW: All
 * PURPOSE: Input validation helper functions
 */

class Validator {
    public static function required(array $data, array $fields): array {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function url(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function int($value, int $min = null, int $max = null): bool {
        if (!is_numeric($value)) return false;
        $int = (int)$value;
        if ($min !== null && $int < $min) return false;
        if ($max !== null && $int > $max) return false;
        return true;
    }

    public static function enum($value, array $allowed): bool {
        return in_array($value, $allowed, true);
    }

    public static function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
