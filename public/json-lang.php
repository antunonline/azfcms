<?php

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
define('APPLICATION_ENV', 'json-lang-production');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

// If expr is not present, or is not a string
if (!isset($_REQUEST['expr']) || !is_string($_REQUEST['expr'])) {
    exit;
}

$expr = $_REQUEST['expr'];
$server = new Azf_Service_Lang();
$server->setResolver("cms", "Azf_Service_Lang_Resolver_Auto");

$renderType = isset($_REQUEST['render-type'])?(string)$_REQUEST['render-type']:"";
switch ($renderType) {
    case "render-in-textarea":
        echo "<html><head><title></title></head><body><textarea>";
        echo $server->executeAndJson($expr);
        echo "</textarea></body></html>";
        break;
    default:
        echo $server->executeAndJson($expr);
        break;
}


