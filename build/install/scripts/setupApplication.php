<?php

if (defined("DEFINE_BUILD_DEPS")) {
    /* @var $this InstallScriptBuilder */

    $this->addVarFromOpt("dbHost");
    $this->addVarFromOpt("dbUser");
    $this->addVarFromOpt("dbPassword");
    $this->addVarFromOpt("dbName");
    $this->addVarFromOpt("templateName");
    return;
}
?><?php

/**
 * Load Zend Framework 
 */
$install[] = function(InstallWorkerLog $log, $dbHost, $dbUser, $dbPassword, $dbName, $templateName) {

            set_include_path(get_include_path() . PATH_SEPARATOR . "../library");
            require('Zend/Loader/Autoloader.php');
            Zend_Loader_Autoloader::getInstance();

            define("APPLICATION_PATH", "../application");
            $configPath = "../application/configs/application.ini";
            $config = new Zend_Config_Ini($configPath, null,
                            array('skipExtends' => true,
                                'allowModifications' => true
                    ));
            
            
            $config->production->resources->db->params->host = $dbHost;
            $config->production->resources->db->params->username = $dbUser;
            $config->production->resources->db->params->password = $dbPassword;
            $config->production->resources->db->params->dbname= $dbName;
            $config->production->defaultTemplate = $templateName;

            $writter = new Zend_Config_Writer_Ini();
            $writter->setConfig($config);
            $writter->write($configPath);


            $log->writeln("Done configuring application.ini");
        };
?>
