<?php

class DijiteditorController extends Azf_Controller_Action {

    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigation() {
        return Zend_Registry::get("navigationModel");
    }

    public function renderAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $content = $this->getValue("content");
        
        echo $content;
    }

    /**
     * Use this action to install the page
     */
    public function installpageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam("id");
        $navigation = $this->getNavigation();
        
        $navigation->setStaticParam($id, 'module', 'default');
        $navigation->setStaticParam($id, 'controller', 'dijiteditor');
        $navigation->setStaticParam($id, 'action', 'render');
    }

    /**
     * Use this action to uninstall the page
     */
    public function uninstallpageAction() {
        
    }
}

