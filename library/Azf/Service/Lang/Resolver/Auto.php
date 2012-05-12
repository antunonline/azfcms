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

    protected function _execute(array $namespaces, array $parameters) {
        $numOfNamespaces = sizeof($namespaces);
        $generated = "";
        
        
        switch($numOfNamespaces){
            case 2:
                $generated = $this->_executeDefaultModuleResolver($namespaces, $parameters);
                break;
            case 3: 
                break;
                $generated = $this->_executeNamedModuleResolver($namespaces, $parameters);
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
        $resolver = new $className();
        
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
        $classNamespace = array_shift($namespaces);
        
        $classSuffix = ucfirst($classNamespace);
        $moduleName = ucfirst($moduleNamespace);
        
        // Construct class name
        $className = $moduleName."_Resolver_".$classSuffix;
        
        // Create instance of the resolver
        $resolver = new $className();
        
        // Pop method name
        $method = array_shift($namespaces);
        
        // Execute resolver
        return $this->_executeResolver($resolver,$classNamespace,$method,$parameters);
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
