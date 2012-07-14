<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

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

$jsSource = "";
$jsConverter = new Azf_Php2Js_Converter();

$recDirIterator= new RecursiveDirectoryIterator(APPLICATION_PATH);
$iterator = new RecursiveIteratorIterator($recDirIterator);
foreach($iterator as $dicDir){
    if(!$dicDir->isFile() || strpos($dicDir->getBasename(),".php")===false){
        continue;
    }
    echo $dicDir->getRealPath()."\n";
    $jsSource.="\n\n".$jsConverter->convertFile($dicDir->getRealPath());
}

$recDirIterator= new RecursiveDirectoryIterator(APPLICATION_PATH."/../library");
$iterator = new RecursiveIteratorIterator($recDirIterator);
foreach($iterator as $dicDir){
    if(!$dicDir->isFile() || strpos($dicDir->getBasename(),".php")===false){
        continue;
    }
    echo $dicDir->getRealPath()."\n";
    $jsSource.="\n\n".$jsConverter->convertFile($dicDir->getRealPath());
}

$jsSource.="\n\n".$jsConverter->convertClass("Azf_Model_Tree_Navigation");

file_put_contents("phpSkeleton.js", $jsSource);
        
