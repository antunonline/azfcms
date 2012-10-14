<?php
/**
 * Description of Test *
 * @author antun
 */
class Vrwhr_Resolver_Test extends Azf_Service_Lang_Resolver{
    
    /**
     *
     * @var Vrwhr_Model_DbTable_Test     */
    protected $_model;
    
    
    /**
     *
     * @var Azf_Service_Lang_ResolverHelper_Dojo
     */
    protected $_dojoHelper;
    
    /**
     * @returnVrwhr_Model_DbTable_Test     */
    protected function _getModel() {
        if(!$this->_model){
            $this->_model = new Vrwhr_Model_DbTable_Test();
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
    
    
    /**
     * 
     * @param array|null $overrideRules
     * @param array|null $forFields
     * @return Azf_Filter_Abstract
     */
    protected function _getFilterInput($overrideRules=array(),$forFields=null) {
        return Azf_Filter_Factory::get("test", "Vrwhr")->getFilterInput($overrideRules,$forFields);
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
    
    
    public function queryMethod(array $query, array $queryArgs, array $queryOptions) {
        $helper = $this->_getDojoHelper();
        $result = $helper->handleQueryRequest($this->_getModel(), "selectJoinUserWhereTitleAND", $query, $queryArgs, $queryOptions);
        return $result;
    }
    
    public function getMethod($record) {
        if(is_scalar($record)){
            $record = array('id'=>$record);
        }
        
        $filterInput = $this->_getFilterInput(null,array('id'));
        $filterInput->setData($record);
        
        if($filterInput->isValid()){
            $id = $filterInput->getEscaped("id");
            return $this->_getDojoHelper()->createAddResponse($this->_getModel()->selectUserWhereId($id));
        } else {
            return $this->_getDojoHelper()->createAddResponse(null, false, $filterInput->getMessages());
        }
    }
    
    public function addMethod($record) {
        $filterInput = $this->_getFilterInput(array(
            'id'=>array(Azf_Filter_Abstract::REMOVE=>true)
        ));
        $filterInput->setData($record);
        
        if($filterInput->isValid()){
            $userEntity = array('id'=>1);
            
            $preparedRecord = $filterInput->getEscaped()+array('userId'=>$userEntity['id']);
            return $this->_getDojoHelper()->createAddResponse($this->_getModel()->insert($preparedRecord));
        } else {
            return $this->_getDojoHelper()->createAddResponse(null, false, $filterInput->getMessages());
        }
    }
    
    public function putMethod($record) {
        $filterInput = $this->_getFilterInput();
        $filterInput->setData($record);
        
        if($filterInput->isValid()){
            $data = $filterInput->getEscaped();
            $id = $data['id'];
            unset($data['id']);
            return $this->_getDojoHelper()->createPutResponse($this->_getModel()->update($data,array('id=?'=>$id)));
        } else {
            return $this->_getDojoHelper()->createPutResponse(null, false, $filterInput->getMessages());
        }
    }
    
    public function removeMethod($record) {
        $filterInput = $this->_getFilterInput(null,array('id'));
        $filterInput->setData($record);
        
        if($filterInput->isValid()){
            $data = $filterInput->getEscaped();
            $id = $data['id'];
            return $this->_getDojoHelper()->createRemoveResponse($this->_getModel()->delete(array('id=?'=>$id)));
        } else {
            return $this->_getDojoHelper()->createRemoveResponse(null, false, $filterInput->getMessages());
        }
    }
    
}

