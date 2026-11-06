<?php
declare(strict_types=1);

/**
 * MODULE: Validation Middleware
 * WORKFLOW: All
 * PURPOSE: Input validation layer per 0810
 */

class ValidationMiddleware {
    public static function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $parts = explode('|', $rule);
            foreach ($parts as $part) {
                if ($part === 'required' && (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null)) {
                    $errors[$field] = 'Required';
                    break;
                }
                if ($part === 'email' && isset($data[$field]) && !Validator::email($data[$field])) {
                    $errors[$field] = 'Invalid email';
                }
                if ($part === 'url' && isset($data[$field]) && !Validator::url($data[$field])) {
                    $errors[$field] = 'Invalid URL';
                }
                if (substr($part, 0, 3) === 'in:' && isset($data[$field])) {
                    $allowed = explode(',', substr($part, 3));
                    if (!in_array($data[$field], $allowed, true)) {
                        $errors[$field] = 'Invalid value';
                    }
                }
            }
        }
        return empty($errors) ? null : $errors;
    }

    public static function assert(array $data, array $rules) {
        $errors = self::validate($data, $rules);
        if ($errors) {
            Response::badRequest('Validation failed', array_keys($errors));
        }
    }
}
