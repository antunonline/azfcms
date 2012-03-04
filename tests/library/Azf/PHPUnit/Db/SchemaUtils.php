<?php

class Azf_PHPUnit_Db_SchemaUtils {
    
    
    /**
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getZendConnection(){
        return Azf_PHPUnit_Db_Utils::getZendConnection();
    }
    
    
    /**
     *
     * @param string $firstName
     * @param string $lastName
     * @return int 
     */
    public static function addUser($firstName, $lastName){
        $return = self::getZendConnection()->insert("User", array(
            "firstName"=>$firstName,
            "lastName"=>$lastName
        ));
        
        return $return;
    }
    
    
    
    /**
     *
     * @param int $id 
     */
    public static function deleteUser($id){
        self::getZendConnection()->delete("User", "id=$id");
    }
    
    
    /**
     *
     * @param string $name 
     * @return int
     */
    public static function addAcl($name){
        $return = self::getZendConnection()->insert("ACLGroup", array(
            "name"=>$name
        ));
        
        return $return;
    }
    
    
    /**
     *
     * @param int $id 
     */
    public static function deleteAcl($id){
        self::getZendConnection()->delete("ACLGroup", "id=$id");
    }
    
    
    /**
     *
     * @param int $userId
     * @param int $aclGroupId 
     * @return int
     */
    public static function bindUser_ACLGroup($userId, $aclGroupId){
        $return = self::getZendConnection()->insert("User_ACLGroup", array(
            "userId"=>$userId,
            "aclGroupId"=>$aclGroupId
        ));
        return $return;
    }
    
    
    /**
     *
     * @param int $navigationId
     * @param int $aclGroupId
     * @return int
     */
    public static function bindNavigation_ACLGroup($navigationId, $aclGroupId){
        $return = self::getZendConnection()->insert("Navigation_ACLGroup", array(
            "navigationId"=>$navigationId,
            "aclGroupId"=>$aclGroupId
        ));
        
        return $return;
    }
    
    
    /**
     *
     * @param int $navigationId
     * @param int $aclGroupId 
     */
    public static function unbindNavigation_ACLGroup($navigationId, $aclGroupId){
        self::getZendConnection()->delete("Navigation_ACLGroup", "navigationId = $navigationId 
                AND aclGroupId = $aclGroupId");
    }
    
}
