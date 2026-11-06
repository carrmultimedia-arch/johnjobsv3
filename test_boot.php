<?php
define('ROOT_PATH', __DIR__);

echo "=== Testing Model.php + CompanyModel.php (BOOT-002 / MDL-001) ===\n";
try {
    require_once ROOT_PATH . '/api/models/Model.php';
    require_once ROOT_PATH . '/api/models/CompanyModel.php';
    $model = new CompanyModel();
    echo "PASS: CompanyModel instantiated with no error.\n";
} catch (\Throwable $e) {
    echo "FAIL: " . get_class($e) . " — " . $e->getMessage() . "\n";
}

echo "\n=== Testing BaseService.php + CompanyService.php (BOOT-001 / SVC-001) ===\n";
try {
    require_once ROOT_PATH . '/api/services/BaseService.php';
    require_once ROOT_PATH . '/api/services/CompanyService.php';
    $service = new CompanyService();
    echo "PASS: CompanyService instantiated with no error.\n";
} catch (\Throwable $e) {
    echo "FAIL: " . get_class($e) . " — " . $e->getMessage() . "\n";
}
