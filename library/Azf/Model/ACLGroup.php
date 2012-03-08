<?php

/**
 * Description of ACLGroup
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_ACLGroup extends Zend_Db_Table_Abstract{
    protected $_name = "ACLGroup";
    protected $_primary = "id";
    
    
    /**
     *
     * @param int $name 
     */
    public function addGroup($name){
        return $this->insert(array('name'=>$name));
    }
    
    
    /**
     * @param int $id
     * @return int
     */
    public function deleteGroup($id){
        return $this->delete(array('id=?'=>$id));
    }
    
    /**
     *
     * @return array
     */
    public function getGroups(){
        return $this->getAdapter()->fetchAll("SELECT * FROM $this->_name;");
    }
    
    /**
     *
     * @param int $id 
     * @return array
     */
    public function getGroup($id){
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?;",array($id));
    }
}

