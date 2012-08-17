<?php

$vars['log'] = new InstallWorkerLog();
$installWorker = new InstallWorker($install,$vars,$resources);


$action = getQueryArg("action");

switch ($action) {
    case "resource":
        $name=  getQueryArg("name");
        $installWorker->flushResource($name);
        break;
    case "restart":
        $installWorker->clearSession();
        $installWorker->runNextJob();
        $installWorker->flushLogs();
        break;
    default:
        $installWorker->runNextJob();
        $installWorker->flushLogs();
        break;
}

ob_end_flush();

?>
