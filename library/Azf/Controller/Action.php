<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Action
 *
 * @author antun
 */
abstract class Azf_Controller_Action extends Zend_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();

        if (in_array($this->getRequest()->getActionName(), array('get', 'set', 'installpage', 'uninstallpage'))) {
            $this->_helper->viewRenderer->setNoRender(true);
            Zend_Layout::getMvcInstance()->disableLayout();
        }
    }

    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigation() {
        return Zend_Registry::get("navigationModel");
    }

    /**
     *
     * @return Azf_Controller_Action_Helper_Context
     */
    public function getContextHelper() {
        return $this->_helper->context;
    }

    public function getNavigationId() {
        return $this->getContextHelper()->getContextId();
    }

    /**
     * Use this action render the content 
     */
    abstract public function renderAction();

    /**
     * Use this action to install the page
     */
    abstract public function installpageAction();

    /**
     * Use this action to uninstall the page
     */
    abstract public function uninstallpageAction();

    /**
     * Get content out of this content plugin 
     */
    public function getAction() {
        Zend_Layout::getMvcInstance()->disableLayout();
        $key = $this->_getParam('key');
        if ($key) {
            if(!is_scalar($key)){
                throw new RuntimeException("request variable 'key' is not a JSON encoded scalar!");
            }
            $value = $this->getValue($key);
        } else {
            $value = $this->getValues();
        }

        $this->_getParam("response")->response = $value;
    }

    /**
     * @return mixed
     */
    public function getValue($key) {
        $values =  (array)$this->getContextHelper()->getStaticParam("values");
        if(isset($values[$key])){
            return $values[$key];
        } else {
            return null;
        }
    }

    
    /**
     *
     * @return array
     */
    public function getValues() {
        return (array) $this->getContextHelper()->getStaticParam('values',array());
    }

    /**
     * Set content into this content plugin
     */
    public function setAction() {
        Zend_Layout::getMvcInstance()->disableLayout();
        $content = $this->_getParam("content");
        $key = $this->_getParam('key');
        
        
        if($key){
            if(!is_scalar($key)){
                throw new RuntimeException("request variable 'key' is not a JSON encoded scalar!");
            }
            $this->setValue($key,$content);
        } else {
            if(is_object($content)){
                throw new RuntimeException("request variable 'content' is not a JSON encoded Object!");
            }
            $this->setValues((array)$content);
        }
    }
    
    
    /**
     *
     * @param string|int $key
     * @param mixed $value 
     */
    public function setValue($key,$value){
        $values = $this->getValues();
        $values[$key] = $value;
        $this->getNavigation()->setStaticParam($this->getNavigationId(), 'values',$values);
    }
    
    
    /**
     *
     * @param array $values 
     */
    public function setValues(array $values){
        $this->getNavigation()->setStaticParam($this->getNavigationId(),'values',$values);
    }
    
}

