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
        return $this->getTemplate()->getRegions($templateIdentifier);
    }
    
    
    
}

