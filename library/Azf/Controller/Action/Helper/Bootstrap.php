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
class Azf_Controller_Action_Helper_Bootstrap extends Zend_Controller_Action_Helper_Abstract {
    public function preDispatch() {
        parent::preDispatch();
        
        // Initialize view helper classes
        $this->_initViewHelpers();
    }

    /**
     * 
     */
    protected function _initViewHelpers() {
        $view = $this->getActionController()->view;
        $this->_registerNavigationViewHelper($view);
    }

    
    /**
     *
     * @param Zend_View $view 
     */
    protected function _registerNavigationViewHelper($view) {
        /* @var $view Zend_View */
        $navigationHelper = new Azf_View_Helper_Navigation();
        $navigationHelper->setContextId($this->getRequest()->getParam("id"));
        $view->addHelperPath(APPLICATION_PATH."/../library/Azf/View/Helper/", "Azf_View_Helper");
        $view->navigation = $navigationHelper;
    }
    
    
    
}

