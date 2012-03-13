<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function _initAzfClassPath() {
        Zend_Loader_Autoloader::getInstance()->registerNamespace("Azf");
    }

    public function _initRpc() {
        $envName = $this->getEnvironment();
        // If we are in json-rpc env
        if (0 === strpos($envName, "json-rpc")) {
            $this->bootstrap("azfClassPath");
            $this->initRpcModule();
            $this->initRpcLoader();
        }
    }
    
    
    public function isMvcEnvironment(){
        return in_array($this->getEnvironment(),array('production','testing','development'));
    }

    protected function initRpcModule() {
        $module = (string) $_GET['module'];
        $moduleDir = APPLICATION_PATH . '/modules/' . $module;
        if ($module && ctype_alpha($module[0]) && ctype_alnum($module) && is_dir($moduleDir)) {
            $resource = new Zend_Application_Resource_Modules(array(
                        $module
                    ));
            $resource->setBootstrap($this);
            $resource->init();
        }
    }

    protected function initRpcLoader() {
        $this->getResourceLoader()
                ->addResourceType("rpcs", "rpcs", "Rpc");
    }

    public function _initRest() {
        if (0 !== strpos($this->getEnvironment(), "json-rest")) {
            return;
        }
        $this->_initRestLoader();
    }

    private function _initRestLoader() {
        $this->getResourceLoader()
                ->addResourceType("rests", "rests", "Rest");
    }
    
    public function _initLog() {
        $logWriter = new Zend_Log_Writer_Null();
        $log = new Zend_Log($logWriter);
        Zend_Registry::set("log",$log);
        Zend_Registry::set("logLevel",  Zend_Registry::isRegistered("logLevel")?Zend_Registry::get("logLevel"):E_ERROR);
    }
    
    public function _initNavigationModel(){
        $dbAdapter = $this->getPluginResource("db")->getDbAdapter();
        
        $navigation = new Azf_Model_Tree_Navigation($dbAdapter);
        Zend_Registry::set("navigationModel", $navigation);
        
        return $navigation;
    }
    
    
    public function _initContextActionHelper(){
        if(!$this->isMvcEnvironment())
            return ;
        
        Zend_Controller_Action_HelperBroker::addPrefix("Azf_Controller_Action_Helper");
    }
    
    
    
    /**
     * Initialize default route
     */
    public function _initRoute(){
        if(!$this->isMvcEnvironment())
            return;
        
        $navigation = $this->getResource("navigationModel");
        $route = new Azf_Controller_Router_Route_Default($navigation);
        $this->getPluginResource("frontcontroller")->getFrontController()->getRouter()->addRoute('default',$route);
    }

}

