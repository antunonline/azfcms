<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Auto
 *
 * @author antun
 */
class Azf_Service_Lang_Resolver_Auto extends Azf_Service_Lang_Resolver {
    
    /**
     * @var array
     */
    protected $_loadedClasses = array();

    protected function _execute(array $namespaces, array $parameters) {
        $numOfNamespaces = sizeof($namespaces);
        $generated = "";
        
        
        switch($numOfNamespaces){
            case 2:
                $generated = $this->_executeDefaultModuleResolver($namespaces, $parameters);
                break;
            case 3: 
                $generated = $this->_executeNamedModuleResolver($namespaces, $parameters);
                break;
            default:
                $generated =  "No Such Method";
                break;
        }
        
        return $generated;
    }
    
    
    /**
     * This method will execute default module resolver. 
     * @param type $namespaces
     * @param type $parameters 
     * @return mixed
     */
    protected function _executeDefaultModuleResolver($namespaces, $parameters){
        $classNamespace = array_shift($namespaces);
        $classSuffix = ucfirst($classNamespace);
        
        // Construct class name
        $className = "Application_Resolver_".$classSuffix;
        
        // Create instance of the resolver
        $resolver = $this->_loadClass($className);
        
        // Pop method name
        $method = array_shift($namespaces);
        
        // Execute resolver
        return $this->_executeResolver($resolver,$classNamespace,$method,$parameters);
    }
    
    
    /**
     * This method will execute default module resolver. 
     * @param type $namespaces
     * @param type $parameters 
     * @return mixed
     */
    protected function _executeNamedModuleResolver($namespaces, $parameters){
        $moduleNamespace = array_shift($namespaces);
        $this->_initModule($moduleNamespace);
        $classNamespace = array_shift($namespaces);
        
        $classSuffix = ucfirst($classNamespace);
        $moduleName = ucfirst($moduleNamespace);
        
        // Construct class name
        $className = $moduleName."_Resolver_".$classSuffix;
        
        // Create instance of the resolver
        $resolver = $this->_loadClass($className);
        
        // Pop method name
        $method = array_shift($namespaces);
        
        // Execute resolver
        return $this->_executeResolver($resolver,$classNamespace,$method,$parameters);
    }
    
    protected function _initModule($moduleName){
        $modulePath = APPLICATION_PATH."/modules/".strtolower($moduleName);
        $bootstrapClassName = ucfirst($moduleName)."_Bootstrap";
        $moduleBootstrapPath = $modulePath."/Bootstrap.php";
        
        if(strtolower($moduleName)=="default"){
            return true;
        }
        
        if(!is_dir($modulePath)){
            return false;
        }
        
        
        if(is_file($moduleBootstrapPath) && is_readable($moduleBootstrapPath)){
           if(!include_once($moduleBootstrapPath)){
               throw new RuntimeException("Could not load module bootstrap file for module '$moduleName'");
           }
           
           if(!class_exists($bootstrapClassName)){
               throw new RuntimeException("Module bootstrap class $bootstrapClassName does not exists");
           }
           
           $bootstrapInstance = new $bootstrapClassName(Zend_Registry::get("application"));
           /* @var $bootstrapInstance Zend_Application_Module_Bootstrap */
           $bootstrapInstance->bootstrap();
        }
        else {
            throw new RuntimeException("Could not load module bootstrap file for module '$moduleName'");
        }
        
        return true;
    }
    
    protected function _loadClass($className){
        if(isset($this->_loadedClasses[$className])) {
            return $this->_loadedClasses[$className];
        } else {
            $this->_loadedClasses[$className] = new $className();
            $this->_loadedClasses[$className]->initialize();
            return $this->_loadedClasses[$className];
        }
    }
    
    
    /**
     * This method will execute resolver if the permissions are acceptable
     * @param Azf_Service_Lang_Resolver $resolver
     * @param string $classNamespace
     * @param string $method
     * @param string $parameters 
     */
    protected function _executeResolver(Azf_Service_Lang_Resolver $resolver,$classNamespace,$method,$parameters){
        return $resolver->execute($classNamespace, array($method), $parameters);
    }

    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

}
