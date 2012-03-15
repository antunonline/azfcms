<?php

/**
 * Description of Bootstrap
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Application_Rpc_Bootstrap extends Azf_Rpc_Provider_Abstract {

    protected function _init() {
        
    }

    protected function _initACL() {
        
    }

    protected function _isAllowed($methodName) {
        if(Zend_Auth::getInstance()->hasIdentity())
            return true;
        else 
            return false;
    }
    
    
    /**
     * Retreive user acl rules
     * @return array 
     */
    public function getAcl(){
        $user = new Azf_Model_User();
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        
        return $user->getUserAclRules($userId);
    }
    
    
    /**
     * @return array
     */
    public function getIdentity(){
        $identity = Zend_Auth::getInstance()->getIdentity();
        return array(
            'id'=>$identity->id,
            'loginName'=>$identity->loginName
        );
    }

}
