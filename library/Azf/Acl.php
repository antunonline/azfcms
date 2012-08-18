<?php

class Azf_Acl{
    
    
    /**
     *
     * @var Azf_Acl
     */
    protected static $_instance;
    
    protected $_resources = array();
    
    /**
     *
     * @var boolean
     */
    protected $_isLoaded = false;
    
    /**
     * 
     * @return Azf_Acl
     */
    public static function getInstance() {
        if(self::$_instance){
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
        if($this->_isBuildInAllowed($accessToResource)){
            return true;
        }
    }
    
    /**
     * 
     * @param string $accessToResource
     * @return boolean
     */
    protected function _isBuildInAllowed($accessToResource) {
        if($accessToResource==="cms.userStereotype.root"){
            return true;
        }  else {
            return false;
        }
    }
    
    protected function _loadAcl(){
        if(Zend_Auth::getInstance()->hasIdentity()){
            
        } else {
            
        }
    }
    
}
