<?="<?php"?>

class <?=strtolower($module)=="default"?"":ucfirst(strtolower($module))."_"?><?= ucfirst(strtolower($name))?>Controller extends Azf_Controller_Action {

    public function renderAction() {
        //$this->_helper->viewRenderer->setNoRender(true);
        
        
    }

    /**
     * Use this action to install the page
     */
    public function installpageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam("id");
        $navigation = $this->getNavigation();
        
        $navigation->setStaticParam($id, 'module', '<?=$module?>');
        $navigation->setStaticParam($id, 'controller', '<?=strtolower($name)?>');
        $navigation->setStaticParam($id, 'action', 'render');
    }
    
    public function uninstallpageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam("id");
        $navigation = $this->getNavigation();
        
        $navigation->deleteStaticParam($id, 'module');
        $navigation->deleteStaticParam($id, 'controller');
        $navigation->deleteStaticParam($id, 'action');
    }

}

