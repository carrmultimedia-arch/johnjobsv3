<?php

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/error_boundary.php';
require_once ROOT_PATH . '/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/api/controllers/',
        ROOT_PATH . '/api/services/',
        ROOT_PATH . '/api/models/',
        ROOT_PATH . '/api/utils/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once ROOT_PATH . '/api/utils/DB.php';

function expectTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function expectSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . " Expected: " . var_export($expected, true) . " Actual: " . var_export($actual, true));
    }
}

function expectNotEmpty($value, string $message): void
{
    if (empty($value)) {
        throw new RuntimeException($message);
    }
}

function runTest(string $name, callable $callback): void
{
    try {
        $callback();
        echo "PASS: {$name}\n";
    } catch (Throwable $e) {
        echo "FAIL: {$name} - {$e->getMessage()}\n";
        throw $e;
    }
}
