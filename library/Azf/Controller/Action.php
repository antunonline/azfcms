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
        
        if(in_array($this->getRequest()->getActionName(),array('get','set','installpage','uninstallpage'))){
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
    public function getContextHelper(){
        return $this->_helper->context;
    }
    
    public function getNavigationId(){
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
        $this->get();
    }
    
    
    /**
     * Generage and echo value that shall be passed to client side editor. 
     */
    abstract public function get();

    /**
     * Set content into this content plugin
     */
    public function setAction(){
        Zend_Layout::getMvcInstance()->disableLayout();
        $content = $this->_getParam("content");
        $this->set($content);
    }
    
    
    /**
     * Store content object submitted by the client utility. 
     * @param $content string
     */
    abstract public function set($content);
}

