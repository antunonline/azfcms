<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Context
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Controller_Action_Helper_Context extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigationModel;
    /**
     *
     * @var int
     */
    protected $contextId;
    
    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigationModel(){
       return $this->navigationModel;
    }
    
    /**
     *
     * @param Azf_Model_Tree_Navigation $model 
     */
    public function setNavigationModel(Azf_Model_Tree_Navigation $model){
        $this->navigationModel = $model;
    }
    
    /**
     * @return int
     */
    public function getContextId(){
        return $this->contextId;
    }
    
    
    /**
     *
     * @param type $id 
     */
    public function setContextId($id){
        $this->contextId = $id;
    }

    
    /**
     * 
     */
    public function init() {
        if(!$this->getNavigationModel()){
            $this->navigationModel = Zend_Registry::get("navigationModel");
        }
        if(!$this->getContextId()){
            $this->contextId = $this->getRequest()->getParam("id");
        }
        
    }
    
    
    /**
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function getStaticParam($name, $default = null){
        return $this->
                getNavigationModel()
                ->getStaticParam($this->getContextId(), $name, $default);
    }
    
    /**
     *
     * @return array|false
     */
    public function getStaticParams(){
        return $this->getNavigationModel()->getStaticParams($this->getContextId());
    }
    
    
    /**
     *
     * @param string $name
     * @param mixed $default 
     * @return mixed
     */
    public function getDynamicParam($name, $default = null){
        return 
        $this->getNavigationModel()->getDynamicParam(
                $this->getContextId(), $name, $default);
    }
    
    
    /**
     * @return array|false
     */
    public function getDynamicParams(){
        return $this->getNavigationModel()
                ->getDynamicParams($this->getContextId());
    }
    
    
    /**
     *
     * @param string $plugin
     * @param string $name
     * @param mixed $default 
     * @return mixed
     */
    public function getPluginParam($plugin, $name, $default = null){
        return 
        $this->getNavigationModel()
                ->getPluginParam($this->getContextId(), $plugin, $name, $default);
    }
    
    
    /**
     *
     * @param string $plugin 
     * @return array|false
     */
    public function getPluginParams($plugin){
        return 
        $this->getNavigationModel()
                ->getPluginParams($this->getContextId(), $plugin);
    }
    
    
    /**
     *
     * @return array
     */
    public function getPluginNames(){
        return $this->getNavigationModel()
                ->getPluginNames($this->getContextId());
    }

}

