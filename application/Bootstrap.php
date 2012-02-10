<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function initAzfClassPath() {
        Zend_Loader_Autoloader::getInstance()->registerNamespace("Azf");
    }

    public function _initRpc() {
        $this->initAzfClassPath();
        $this->_initRpcModule();
        $this->_initRpcLoader();
        
    }
    
    protected function _initRpcModule(){
        $module = (string) $_GET['module'];
        $moduleDir = APPLICATION_PATH.'/modules/'.$module;
        if($module && ctype_alpha($module[0]) && ctype_alnum($module) && is_dir($moduleDir)){
            $resource = new Zend_Application_Resource_Modules(array(
                $module
            ));
            $resource->setBootstrap($this);
            $resource->init();
        }
    }

    protected function _initRpcLoader() {
        $this->getResourceLoader()
                ->addResourceType("rpcs", "rpcs", "Rpc");
    }

}

