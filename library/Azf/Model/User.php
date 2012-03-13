<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_User extends Zend_Db_Table_Abstract{
    protected $_name = "User";
    protected $_primary = "id";
    
    
    /**
     *
     * @param string $loginName
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string $email 
     * @return int
     */
    public function createUser($loginName, $password, $firstName, $lastName, $email){
        $verificationKey  = md5(rand(0, PHP_INT_MAX)."&".rand(0, PHP_INT_MAX));
        $passwordHash = sha1($password);
        return $this->insert(array(
            'loginName'=>$loginName,
            'password'=>$passwordHash,
            'firstName'=>$firstName,
            'lastName'=>$lastName,
            'email'=>$email,
            'verificationKey'=>$verificationKey
        ));
    }
    
    
    /**
     * @param int id
     * @return string|false
     */
    public function getValidationKey($id){
        return $this->getAdapter()->fetchOne("SELECT validationKey FROM $this->_name WHERE $this->_primary = ?;",array($id));
    }
    
    
    /**
     *
     * @param string $key 
     * @return boolean
     */
    public function verifyUser($key){
        return (boolean) $this->getAdapter()->update($this->_name, array('verified'=>1),array('verificationKey=?'=>$key));
    }
    
    
    /**
     *
     * @param int $id
     * @param string $firstName 
     * @return boolean
     */
    public function setUserFirstName($id, $firstName){
        return (bool) $this->update(array('firstName'=>$firstName),array('id=?'=>$id));
    }
    
    
    /**
     *
     * @param int $id
     * @param string $lastName 
     * @return boolean
     */
    public function setUserLastName($id, $lastName){
        return (bool) $this->update(array('lastName'=>$lastName),array('id=?'=>$id));
    }
    
    
    /**
     *
     * @param int $id
     * @param string $password 
     * @return boolean
     */
    public function setUserPassword($id, $password){
        return (bool) $this->update(array('password'=>sha1($password)),array('id=?'=>$id));
    }
    
    
    /**
     *
     * @param int $id 
     * @return boolean
     */
    public function deleteUser($id){
      return (bool) $this->delete(array('id=?'=>$id));  
    }
    
    
    /**
     *
     * @param int $id 
     * @return array|null
     */
    public function getUser($id){
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?;",array($id));
    }
    
    
    /**
     *
     * @param string $loginName 
     * @return array|null
     */
    public function getUserByLoginName($loginName){
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE loginName = ?;",array($loginName));
    }
    
    
    /**
     *  Fetch user acl rules
     * @param int $id
     * @return array
     */
    public function getUserAclRules($id){
        return $this->getAdapter()->fetchAll("call user_fetchAclRules (?);",array($id));
    }
    
    
    
    
}

