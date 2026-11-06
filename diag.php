<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

echo "<h2>Diagnostics</h2>";

// 1. Check config.php exists and loads
echo "<h3>1. Config Load</h3>";
try {
    require_once __DIR__ . '/config.php';
    echo "config.php: LOADED<br>";
    echo "DB_DRIVER: " . (defined('DB_DRIVER') ? DB_DRIVER : 'NOT DEFINED') . "<br>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
} catch (Throwable $e) {
    echo "config.php FAILED: " . $e->getMessage();
}

// 2. Check DB.php loads
echo "<h3>2. DB Utility</h3>";
try {
    require_once __DIR__ . '/api/utils/DB.php';
    echo "DB.php: LOADED<br>";
} catch (Throwable $e) {
    echo "DB.php FAILED: " . $e->getMessage();
}

// 3. Try database connection
echo "<h3>3. DB Connection</h3>";
try {
    $db = DB::getInstance();
    echo "DB CONNECTED<br>";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables: " . json_encode($tables);
} catch (Throwable $e) {
    echo "DB FAILED: " . $e->getMessage();
}

// 4. Check file permissions
echo "<h3>4. Permissions</h3>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script dir: " . __DIR__ . "<br>";
echo "jobhunt.db exists: " . (file_exists(__DIR__ . '/database/jobhunt.db') ? 'YES' : 'NO') . "<br>";
if (file_exists(__DIR__ . '/database/jobhunt.db')) {
    echo "jobhunt.db readable: " . (is_readable(__DIR__ . '/database/jobhunt.db') ? 'YES' : 'NO') . "<br>";
    echo "jobhunt.db writable: " . (is_writable(__DIR__ . '/database/jobhunt.db') ? 'YES' : 'NO') . "<br>";
}