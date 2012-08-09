<?php

class Application_Resolver_User extends Azf_Service_Lang_Resolver {
    
    /**
     *
     * @var Application_
     */
    protected $userModel;
    
    public function getUserModel() {
        return $this->userModel;
    }

    public function setUserModel($userModel) {
        $this->userModel = $userModel;
    }

        
    public function initialize() {
        parent::initialize();
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

}
