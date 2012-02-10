<?php

/**
 * // TODO
 *
 * @author Antun Horvat <at> it-branch.com
 */
class User_Rpc_User extends Azf_Rpc_Provider_Abstract {

    protected function _init() {
        
    }

    protected function _initACL() {
        
    }

    protected function _isAllowed($methodName) {
        return true;
    }
    
    
    /**
     *
     * @param int $id 
     * @return array
     */
    public function getUser($id){
        return array(
            "id"=>$id,
            'firstName'=>"First",
            "lastName"=>"Last"
        );
    }

}

