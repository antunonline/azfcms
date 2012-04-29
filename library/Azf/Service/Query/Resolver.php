<?php

abstract class Azf_Service_Query_Resolver {

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
    public function execute($registeredNamespace, array $namespaces, array $parameters){
        $this->setRegisteredNamespace($registeredNamespace);
        $this->setNamespaces($namespaces);
        $this->setParameters($parameters);
        
        return $this->_execute($namespaces, $parameters);
    }
    
    
    
    abstract protected function _execute(array $namespaces, array $parameters);



}
