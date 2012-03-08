<?php

/**
 * Description of ACLGroup
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_UserACL extends Zend_Db_Table_Abstract{
    protected $_name = "User_ACLGroup";
    protected $_primary = "id";
    
    /**
     *
     * @param int $userId
     * @param int $aclGroupId 
     */
    public function bind($userId, $aclGroupId){
        return $this->insert(array(
            'userId'=>$userId,
            'aclGroupId'=>$aclGroupId
        ));
    }
    
    /**
     *
     * @param int $userId
     * @param int $aclGroupId 
     */
    public function unBind($userId, $aclGroupId){
        return $this->delete(array(
            'userId=?'=>$userId,
            'aclGroupId=?'=>$aclGroupId
        ));
    }
}

