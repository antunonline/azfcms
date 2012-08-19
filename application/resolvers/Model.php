<?php

class Application_Resolver_Model extends Azf_Service_Lang_Resolver {
        
    public function initialize() {
        parent::initialize();
    }

    /**
     *
     * @param string $namespace 
     * @return string
     */
    protected function _getModelClass($namespace){
        $class = "Application_Model_DbTable_".ucfirst($namespace);
        return $class;
    }
    
    protected function _classExists($className){
        return Zend_Loader_Autoloader::getInstance()->autoload($className);
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return Azf_Acl::hasAccess("resource.admin.rw");
    }

    
    /**
     *
     * @param array $namespaces
     * @param array $parameters
     * @return null |mixed null if no class or method is found
     */
    protected function _execute(array $namespaces, array $parameters) {
        $modelName = array_shift($namespaces);
        $method = array_shift($namespaces);
        $class = $this->_getModelClass($modelName);
        
        if(!$this->_classExists($class)){
            return null;
        }
        
        $instance = $this->getInstance($class);
        // Check method
        if(method_exists($instance, $method)){
            $response = call_user_method_array($method, $instance, $parameters);
            return $this->_normalizeResponse($response);
        } else {
            return null;
        }
        
    }
    
    public function _normalizeResponse($response){
        if(get_class($response)=="Zend_Db_Table_Row"){
            return $response->toArray();
        } else if(get_class($response)=="Zend_Db_Table_Rowset") {
            return $response->toArray();
        } else {
            return $response;
        }
    }

    public function getInstance($class) {
        return new $class();
    }

}
