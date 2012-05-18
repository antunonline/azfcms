<?php

class DijiteditorController extends Zend_Controller_Action
{
    
    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigation(){
        return Zend_Registry::get("navigationModel");
    }

    public function init()
    {
        
    }

    public function renderAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        echo $this->_helper->context->getStaticParam("body");
    }

    
    /**
     * Use this action to install the page
     */
    public function installpageAction(){
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam("id");
        $navigation = $this->getNavigation();
        $navigation->setStaticParam($id, "body", null);
        
    }
    
    
    /**
     * Use this action to uninstall the page
     */
    public function uninstallpageAction(){
        
    }


}

