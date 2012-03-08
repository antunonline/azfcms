<?php

/**
 * Description of NavigationAcl
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_NavigationAcl extends Zend_Db_Table_Abstract {
    
    
    /**
     *
     * @param int $navigationId
     * @param int $aclGroupId 
     * @return int
     */
    public function bind($navigationId, $aclGroupId){
        return $this->insert(array(
            'navigationId'=>$navigationId,
            'aclGroupId'=>$aclGroupId
        ));
    }
    
    
    /**
     *
     * @param int $navigationId
     * @param int $aclGroupId 
     * @return int
     */
    public function unBind($navigationId, $aclGroupId){
        return $this->delete(array("navigationId=?"=>$navigationId, 'aclGroupId=?'=>$aclGroupId));
    }
}
