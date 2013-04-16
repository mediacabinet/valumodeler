<?php
/*
* Set error reporting to the level to which VALU code must comply.
*/
error_reporting( E_ALL | E_STRICT );

ini_set('display_errors', 1);

$applicationRoot = __DIR__ . '/../../../../';
chdir($applicationRoot);

define("APPLICATION_TEST_CONFIG_FILE", $applicationRoot . 'config/tests.config.php');

// Setup autoloading
include __DIR__ . '/_autoload.php';