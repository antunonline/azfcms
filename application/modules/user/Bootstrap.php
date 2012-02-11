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
        $envName = $this->getEnvironment();
        
        if(0 === strpos($envName,"json-rpc")){
            $this->_initRpcLoader();
        }
    }
    
    public function _initRpcLoader(){
        $this->getResourceLoader()->addResourceType("rpcs", "rpcs","Rpc");
    }
}

