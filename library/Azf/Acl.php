<?php

class Azf_Acl {

    /**
     *
     * @var Azf_Acl
     */
    protected static $_instance;

    /**
     *
     * @var array
     */
    protected $_resources = array();

    /**
     *
     * @var boolean
     */
    protected $_isLoaded = false;
    
    
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     * @return Azf_Model_User
     */
    public function getUserModel() {
        return new Azf_Model_User();
    }
    
    
    /**
     * @return Zend_Session_Namespace
     */
    public function getSession() {
        if(!$this->_session){
            $this->_session = new Zend_Session_Namespace('acl');
        }
        return $this->_session;
        
    }

    /**
     * 
     * @return Azf_Acl
     */
    public static function getInstance() {
        if (self::$_instance) {
            return self::$_instance;
        } else {
            return self::$_instance = new Azf_Acl();
        }
    }

    /**
     * 
     * @param string $accessToResource
     */
    public function isAllowed($accessToResource) {
        if(!$this->_isLoaded){
            $this->_loadAcl();
        }
        
        if ($this->_isBuildInAllowed()) {
            return true;
        }
        
        
        return in_array($accessToResource,$this->_resources);
    }

    /**
     * 
     * @param string $accessToResource
     * @return boolean
     */
    protected function _isBuildInAllowed() {
        if (in_array("cms.userStereotype.root",  $this->_resources)) {
            return true;
        } else {
            return false;
        }
    }

    protected function _loadAcl() {
        $session = $this->getSession();
        
        
        if ($session->__isset("acl")) {
            $this->_resources = $session->__get("acl");
            return;
        } else {

            if (Zend_Auth::getInstance()->hasIdentity()) {
                $user = Zend_Auth::getInstance()->getIdentity();
                $userId = $user['id'];
                $this->_resources = $aclList = $this->getUserModel()->getUserAcl($userId);
            } else {
                $this->_resources = $aclList = $this->getUserModel()->getGuestAcl();
            }
            
            $session->__set("acl", $aclList);
        }
        
        $this->_isLoaded = true;
    }
    
    
    public function reset() {
        $this->_resources=array();
        $this->_isLoaded=false;
        $this->getSession()->__unset("acl");
    }
    
    
    /**
     * 
     * @param string $toResource
     * @return boolean
     */
    public static function hasAccess($toResource) {
        return self::getInstance()->isAllowed($toResource);
    }

}
