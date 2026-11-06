<?php
/**
 * JobHunt Command Center - Application Bootstrap
 * johnjobsv2/app.php
 * 
 * Standalone app entry point. Loads config and core.
 */

require_once __DIR__ . '/config.php';
require_once API_ROOT . '/utils/Response.php';
require_once API_ROOT . '/utils/DB.php';

// App is ready
// Use this file for CLI scripts or background jobs
