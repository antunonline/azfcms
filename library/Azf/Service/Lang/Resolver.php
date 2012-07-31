<?php

abstract class Azf_Service_Lang_Resolver {
    
    /**
     *
     * @var array
     */
    protected $_helpers = array();

    protected $registeredNamespace = "";
    protected $namespaces = array();

    /**
     *
     * @var array 
     */
    protected $parameters = array();

    /**
     *
     * @return string
     */
    public function getRegisteredNamespace() {
        return $this->registeredNamespace;
    }

    /**
     *
     * @param string $registeredNamespace 
     */
    public function setRegisteredNamespace($registeredNamespace) {
        $this->registeredNamespace = $registeredNamespace;
    }

    /**
     *
     * @return array
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     *
     * @param array $namespaces 
     */
    public function setNamespaces(array $namespaces) {
        $this->namespaces = $namespaces;
    }

    /**
     *
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     *
     * @param array $parameters 
     */
    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     *
     * @param string $registeredNamespace
     * @param array $namespaces
     * @param array $parameters
     * @throws  BadMethodCallException
     */
    public function execute($registeredNamespace, array $namespaces, array $parameters) {
        $this->setRegisteredNamespace($registeredNamespace);
        $this->setNamespaces($namespaces);
        $this->setParameters($parameters);

        if (!$this->isAllowed($namespaces, $parameters)) {
            return "Not Allowed";
        } else {
            return $this->_execute($namespaces, $parameters);
        }
    }

    /**
     * Use this method to initialize resolver 
     */
    public function initialize() {
        
    }

    protected function _execute(array $namespaces, array $parameters) {
        if (count($namespaces) != 1) {
            return false;
        }

        $method = array_shift($namespaces) . "Method";


        if (method_exists($this, $method)) {
            return call_user_method_array($method, $this, $parameters);
        } else {
            return false;
        }
    }

    protected function isAllowed($namespaces, $parameters) {
        return false;
    }
    
    
    /**
     * 
     * @param object $helper
     */
    protected function _loadHelper($helper){
        $className = "Azf_Service_Lang_ResolverHelper_".ucfirst($helper);
        if(Zend_Loader_Autoloader::getInstance()->autoload($className)){
            return new $className;
        } else {
            throw new Zend_Loader_Exception("Could not load class $className");
        }
    }
    
    
    /**
     * 
     * @param string $helper
     * @return object
     */
    public function getHelper($helper){
        if(isset($this->_helpers[$helper])){
            return $this->_helpers[$helper];
        } else {
            return $this->_helpers[$helper] = $this->_loadHelper($helper);
        }
    }

}
