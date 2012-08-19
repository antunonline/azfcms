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
class Azf_Auth_Adapter_User implements Zend_Auth_Adapter_Interface {

    /**
     *
     * @var Azf_Model_User
     */
    protected $user = null;

    /**
     *
     * @var string
     */
    protected $loginName;

    /**
     *
     * @var string
     */
    protected $password;
    
    /**
     *
     * @var string
     */
    public $signKey = "";

    /**
     *
     * @return string
     */
    public function getLoginName() {
        return $this->loginName;
    }

    /**
     *
     * @param string $loginName 
     */
    public function setLoginName($loginName) {
        $this->loginName = (string)$loginName;
    }

    /**
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     *
     * @param string $password 
     */
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function setSignKey($signKey) {
        $this->signKey = $signKey;
    }
    
    public function getSignKey() {
        return $this->signKey;
    }

    /**
     *
     * @param Azf_Model_User $user 
     */
    public function __construct(Azf_Model_User $user) {
        $this->user = $user;
    }

    /**
     *  @return Zend_Auth_Result
     */
    public function authenticate() {
        $user = $this->user->getUserByLoginName($this->getLoginName());
        $code = Zend_Auth_Result::FAILURE;
        $identity = array();
        
        if($user){
            $signKey = $this->getSignKey();
            $actualPassword = sha1($signKey.$user['password']);
            $providedPassword = $this->getPassword();
            if($actualPassword==$providedPassword){
                $code = Zend_Auth_Result::SUCCESS;
                unset($user['password']);
                unset($user['verificationKey']);
                
                $identity = $user;
            }
        }
        
        $result = new Zend_Auth_Result($code, $identity);
        return $result;
        
    }

}
