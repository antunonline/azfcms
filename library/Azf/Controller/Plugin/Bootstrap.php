<?php


/**
 * Description of Bootstrap
 *
 * @author antun
 */
class Azf_Controller_Plugin_Bootstrap extends Zend_Controller_Plugin_Abstract{
    
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        parent::routeShutdown($request);
        
        // Configure controller helper classes
        $this->_initControllerHelpers();
        
        $this->_initModule($request);
    }
    
    /**
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function _initModule(Zend_Controller_Request_Abstract $request) {
        $module = $request->getModuleName();
        
        Azf_Bootstrap_Module::getInstance()->load($module);
    }

    /**
     * This method will initialize controller helpers 
     */
    public function _initControllerHelpers() {
        // Register azf action helper path
        Zend_Controller_Action_HelperBroker::addPrefix("Azf_Controller_Action_Helper_");
        
        // Add Bootstrap helper
        Zend_Controller_Action_HelperBroker::addHelper(new Azf_Controller_Action_Helper_Bootstrap());
        
    }
    
    
    
    
}

