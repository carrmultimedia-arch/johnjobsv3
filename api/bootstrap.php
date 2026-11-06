<?php

require_once __DIR__ . '/../error_boundary.php';

define('ROOT_PATH', dirname(__DIR__));

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

require_once ROOT_PATH . '/api/router.php';
