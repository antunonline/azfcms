<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Loader
 *
 * @author antun
 */
class Azf_Bootstrap_Module {
    protected static $_self=null;
    
    /**
     *
     * @var array
     */
    protected $_loadedModules = array();
    
    /**
     * 
     * @return Azf_Bootstrap_Module
     */
    public static function getInstance() {
        if(!self::$_self){
            self::$_self = new self();
        }
        
        return self::$_self;
    }
    
    
    /**
     * 
     * @param string $module
     * @return boolean
     * @throws RuntimeException
     */
    public function load($module) {
        $module = strtolower($module);
        if(in_array($module, $this->_loadedModules)){
            return;
        } else {
            $this->_loadedModules[] = $module;
        }
        
        if ($module == "default" || $module == "application") {
            return;
        }
        
        $module = lcfirst($module);
        $modulePath = APPLICATION_PATH . "/modules/" . $module;
        $bootstrapPath = $modulePath . "/Bootstrap.php";
        $bootstrapClassName = ucfirst($module) . "_Bootstrap";

        if (!is_dir($modulePath)) {
            throw new RuntimeException("Module directory for module \"$module\" does not exist");
        }


        if (is_readable($bootstrapPath)) {
            include_once($bootstrapPath);
            if (class_exists($bootstrapClassName)) {
                $bootstrapInstance = new $bootstrapClassName(Zend_Registry::get("application"));
                /* @var $bootstrapInstance Zend_Application_Module_Bootstrap */
                $bootstrapInstance->bootstrap();
            } else {
                throw new RuntimeException("Bootstrap file is not defined");
            }
        }
    }
    
    protected function _startsWith($body,$start) {
        if(strpos($body, $start)===0){
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * 
     * @return array
     */
    protected function _callInitMethods() {
        $reflectionClass = new ReflectionClass($this);
        $reflectionMethods = $reflectionClass->getMethods();
        
        foreach($reflectionMethods as $reflectionMethod){
            /* @var $reflectionMethod ReflectionMethod */
            $methodName = $reflectionMethod->getName();
            if($this->_startsWith($methodName, "_init")){
               $reflectionMethod->invoke($this);
            }
        }
        
    }
    
    public function bootstrap(){
        $this->_callInitMethods();
    }
    
    public function _initLoader() {
        
    }
}
