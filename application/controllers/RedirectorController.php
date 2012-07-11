<?php

class RedirectorController extends Azf_Controller_Action {


    public function installpageAction() {
        $navigation = $this->getNavigation();
        $id = $this->getNavigationId();
        $navigation->setStaticParam($id, "module", "default");
        $navigation->setStaticParam($id, "controller", "redirector");
        $navigation->setStaticParam($id, "action", "render");
        $this->_setUrl("");
    }
    
    protected function _setUrl($url){
        $this->getNavigation()->setStaticParam($this->getNavigationId(), "url", trim($url),"    ");
    }

    public function renderAction() {
        $url = $this->getValue("url");
        $redirector = $this->_helper->redirector;
        /* @var $redirector Zend_Controller_Action_Helper_Redirector */
        $redirector->gotoUrlAndExit($url);
    }


    public function uninstallpageAction() {
        
    }

}

