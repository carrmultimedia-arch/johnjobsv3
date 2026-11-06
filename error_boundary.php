<?php
/**
 * error_boundary.php
 * Include this at the very top of api/bootstrap.php AND root/index.php.
 * Closes the entire "raw PHP error leaks past clean output" bug class,
 * system-wide, instead of patching it endpoint by endpoint.
 */

// Never let raw PHP-formatted HTML errors reach the browser.
ini_set('display_errors', '0');
error_reporting(E_ALL);

function boundary_respond(string $message, int $code = 500): void {
    // API paths get clean JSON; page loads get a plain readable message.
    $isApi = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0;
    http_response_code($code);
    if ($isApi) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $message]);
    } else {
        echo "<div style='font-family:sans-serif;padding:2rem;color:#c0392b'>Something broke: "
            . htmlspecialchars($message) . "</div>";
    }
}

set_error_handler(function ($severity, $message, $file, $line) {
    error_log("[PHP ERROR] $message in $file:$line");
    boundary_respond("$message in " . basename($file) . ":$line");
    exit;
});

set_exception_handler(function (Throwable $e) {
    error_log("[UNCAUGHT] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    boundary_respond($e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine());
    exit;
});

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("[FATAL] " . $err['message'] . " in " . $err['file'] . ":" . $err['line']);
        boundary_respond($err['message'] . " in " . basename($err['file']) . ":" . $err['line']);
    }
});