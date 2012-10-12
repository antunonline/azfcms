<?php

/**
 * Description of NewsCategory
 *
 * @author antun
 */
class Vrwhr_Resolver_NewsCategory extends Azf_Service_Lang_Resolver{
    
    /**
     *
     * @var Vrwhr_Model_DbTable_NewsCategory
     */
    protected $_model;
    
    
    /**
     *
     * @var Azf_Service_Lang_ResolverHelper_Dojo
     */
    protected $_dojoHelper;
    
    /**
     * @return Vrwhr_Model_DbTable_NewsCategory
     */
    protected function _getModel() {
        if(!$this->_model){
            $this->_model = new Vrwhr_Model_DbTable_NewsCategory();
        }
        
        return $this->_model;
    }
    
    
    /**
     * @return Azf_Service_Lang_ResolverHelper_Dojo
     */
    protected function _getDojoHelper() {
        if(!$this->_dojoHelper){
            $this->_dojoHelper = new Azf_Service_Lang_ResolverHelper_Dojo();
        }
        
        return $this->_dojoHelper;
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
    
    
    public function queryMethod(array $query, array $queryArgs, array $queryOptions) {
        $helper = $this->_getDojoHelper();
        $result = $helper->handleQueryRequest($this->_getModel(), "selectJoinUserWhereTitleAND", $query, $queryArgs, $queryOptions);
        return $result;
    }
    
}

