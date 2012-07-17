<?php

class DijiteditorController extends Azf_Controller_Action {

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
    
    public function uninstallpageAction() {
        $id = $this->_getParam("id");
        $navigation = $this->getNavigation();
        
        $navigation->deleteStaticParam($id, 'module');
        $navigation->deleteStaticParam($id, 'controller');
        $navigation->deleteStaticParam($id, 'action');
    }

}

