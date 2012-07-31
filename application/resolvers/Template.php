<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Template
 *
 * @author antun
 */
class Application_Resolver_Template extends Azf_Service_Lang_Resolver{
    
    /**
     *
     * @var Azf_Template_Descriptor
     */
    protected $template;
    
    
    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigationModel;
    
    /**
     * 
     * @return Azf_Template_Descriptor
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * 
     * @param Azf_Template_Descriptor $template
     */
    public function setTemplate(Azf_Template_Descriptor $template) {
        $this->template = $template;
    }
    
    
    /**
     * 
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigationModel() {
        if(!$this->navigationModel){
            $this->setNavigationModel(Zend_Registry::get("navigationModel"));
        }
        return $this->navigationModel;
    }

    /**
     * 
     * @param Azf_Model_Tree_Navigation $navigationModel
     */
    public function setNavigationModel(Azf_Model_Tree_Navigation $navigationModel) {
        $this->navigationModel = $navigationModel;
    }

        
    /**
     * Initialize obj. 
     */
    public function initialize() {
        parent::initialize();
        if(!$this->getTemplate()){
            $this->setTemplate(new Azf_Template_Descriptor());
        }
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

        
    protected function _execute(array $namespaces, array $parameters) {
        if(count($namespaces)!=1){
            return false;
        }
        
        $method = array_shift($namespaces)."Method";
        
        
        if(method_exists($this, $method)){
            return call_user_method_array($method, $this, $parameters);
        } else {
            return false;
        }
    }
    
    /**
     * 
     * @param string $templateIdentifier
     * @return array|null
     */
    public function getTemplateRegionsMethod($templateIdentifier){
        if(!is_string($templateIdentifier)) {
            return null;
        }
        
        // TODO implement pagination
        $regions = $this->getTemplate()->getRegions($templateIdentifier);
        return array(
            'data'=>$regions,
            'total'=>sizeof($regions)
        );
    }
    
    
    /**
     * 
     * @param int $navigationId
     * @return array|null
     */
    public function getTemplateRegionsForNavigationMethod($navigationId){
        $navigationModel = $this->getNavigationModel();
        
        $templateIdentifier = $navigationModel->getStaticParam($navigationId, "templateIdentifier");
        if(!$templateIdentifier){
            $templateIdentifier=  Zend_Registry::get("defaultTemplate");
        }
        
        return $this->getTemplate()->getRegions($templateIdentifier);
    }
    
    
    /**
     * 
     * @return array|null
     */
    public function getDefaultTemplateRegionsMethod(){
        $navigationModel = $this->getNavigationModel();
        
        $templateIdentifier=  Zend_Registry::get("defaultTemplate");
        
        $regions = $this->getTemplate()->getRegions($templateIdentifier);
        return array(
            'data'=>$regions,
            'total'=>$regions
        );
    }
    
    
    
}

