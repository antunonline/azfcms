<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bootstrap
 *
 * @author Antun Horvat <at> it-branch.com
 */
class User_Bootstrap extends Zend_Application_Module_Bootstrap{
    
    public function _initRpc(){
        $this->_initRpcLoader();
    }
    
    public function _initRpcLoader(){
        $this->getResourceLoader()->addResourceType("rpcs", "rpcs","Rpc");
    }
}

