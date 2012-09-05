<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author antun
 */
class Test_IndexController extends Azf_Controller_Action{

    public function installpageAction() {
        
    }

    public function renderAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        echo "THIS IS COOL11";
    }

    public function uninstallpageAction() {
        
    }
}

