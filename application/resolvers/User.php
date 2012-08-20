<?php

class Application_Resolver_User extends Azf_Service_Lang_Resolver {
    
    const LOGIN_PASSWORD_SIGN_HASH = 'loginSessionSignHash';

    /**
     *
     * @var Azf_Model_User
     */
    protected $userModel;

    /**
     * 
     * @return Azf_Model_User
     */
    public function getUserModel() {
        if (!$this->userModel) {
            $this->setUserModel(new Azf_Model_User());
        }
        return $this->userModel;
    }

    /**
     * 
     * @param Azf_Model_User $userModel
     */
    public function setUserModel($userModel) {
        $this->userModel = $userModel;
    }

    public function initialize() {
        parent::initialize();
    }
    
    /**
     * 
     * @return Azf_Service_Lang_ResolverHelper_Dojo
     */
    public function getDojoHelper() {
        return $this->getHelper("dojo");
    }
    
    /**
     * @return Zend_Session_Namespace
     */
    public function getSession() {
        return new Zend_Session_Namespace("service.resource.user");
    }

    protected function isAllowed($namespaces, $parameters) {
        $resource = $this->getNamespaceDocCommentResource($namespaces[0]);
        
        if($resource){
            return Azf_Acl::hasAccess($resource);
        } else {
            return Azf_Acl::hasAccess("resource.admin.rw");
        }
        
    }

    /**
     * 
     * @param mixed $query
     * @param mixed $queryOptions
     * @param mixed $staticOptions
     * @return array
     */
    public function queryUsersMethod(array $query, array $queryOptions, $staticOptions) {
        $model = $this->getUserModel();
        $dojoHelper = $this->getHelper("dojo");  /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */

        $start = $dojoHelper->getQueryOptionsStart($queryOptions);
        $count = $dojoHelper->getQueryOptionsCount($queryOptions);
        $returnCols = array('id', 'loginName', 'firstName', 'lastName', 'email','verified','verificationKey');
        $searchLogin = $dojoHelper->getQueryStringParam($query, 'loginName');
        $searchEmail = $dojoHelper->getQueryStringParam($query, "email");


        $resultSet = $model->fetchByLoginNameAndEmail($searchLogin, $searchEmail, $count, $start);
        $rows = array();
        foreach ($resultSet as $row) {
            $rows[] = array_intersect_key($row->toArray(), array_flip($returnCols));
        }
        return $dojoHelper->constructQueryResult($rows);
    }

    public function updateUserMethod(array $user) {
        unset($user['password']);
        unset($user['loginName']);
        if (!isset($user['id']) || !ctype_digit($user['id']))
            $this->getUserModel()->update($user, $where);
        return true;
    }

    /**
     * 
     * @param array $user
     * @return boolean|array
     */
    public function addUserMethod(array $user) {
        $filter = Azf_Filter_Factory::get("user");
        $filterInput = $filter->getFilterInput(array(
            'id' => array(Azf_Filter_Abstract::REMOVE => true),
            'verified' => array(Azf_Filter_Abstract::REMOVE => true),
            'verificationKey' => array(Azf_Filter_Abstract::REMOVE => true)
                ));

        $filterInput->setData($user);
        if (!$filterInput->isValid()) {
            return $this->getDojoHelper()
                    ->createAddResponse(null, false, $filterInput->getMessages());
        }
        $newUser = $filterInput->getEscaped();
        $newUser['password'] = sha1($filterInput->getUnescaped('password'));
        $this->getUserModel()->insert($newUser);
        return $this->getDojoHelper()->createAddResponse();
    }

    public function saveUserMethod(array $user) {
        $filter = Azf_Filter_Factory::get("user");
        $filterInput = $filter->getFilterInput(
                array(
                    'password' => array(
                        Azf_Filter_Abstract::REMOVE => true
                    ),
                    'loginName' => array(
                        Azf_Filter_Abstract::REMOVE => true
                    )
                )
        );
        $filterInput->setData($user);
        

        if (!$filterInput->isValid()) {
            return $this->getDojoHelper()->createPutResponse(null, false, $filterInput->getMessages());
        }

        $newUser = $filterInput->getEscaped();
        unset($newUser['id']);
        $id = $filterInput->id;

        $this->getUserModel()->update($newUser, array('id=?' => $id));
        return $this->getDojoHelper()->createPutResponse();
    }
    
    
    /**
     * 
     * @param array $user
     * @return array
     */
    public function removeUserMethod(array $user) {
        $filterInput = Azf_Filter_Factory::get("user")->getFilterInput(array(), array('id'));
        $filterInput->setData($user);
        
        if($filterInput->isValid()==false){
            return $this->getDojoHelper()->createRemoveResponse(null, false, $filterInput->getMessages());
        }
        
        $id = $filterInput->id;
        $this->getUserModel()->delete(array('id=?'=>$id));
        return $this->getDojoHelper()->createRemoveResponse($id);
    }
    
    
    /**
     * 
     * @return string
     * @resource resource.user.login
     */
    public function getPasswordSignKeyMethod() {
        $session = $this->getSession();
        
        if(!$session->__isset(self::LOGIN_PASSWORD_SIGN_HASH)){
            $key = sha1(sha1(rand(0, PHP_INT_MAX)).sha1(rand(0, PHP_INT_MAX)));
            $session->__set(self::LOGIN_PASSWORD_SIGN_HASH,$key);            
        }
        
        return $this->getDojoHelper()
                ->createGetResponse($session->__get(self::LOGIN_PASSWORD_SIGN_HASH));
    }
    
    /**
     * 
     * @param type $loginName
     * @param type $signedPasswordHash
     * @return array
     * @resource resource.user.login
     */
    public function loginMethod($loginName, $signedPasswordHash) {
        $filter = Azf_Filter_Factory::get("user")
                ->getFilterInput(array(
                    'loginName'=>array(
                    Azf_Filter_Abstract::REMOVE_VALIDATORS=>array('db_NoRecordExists')
                    )
                ), array('loginName'));
        
        $filter->setData(array("loginName"=>$loginName));
        if($filter->isValid()==false||!is_string($signedPasswordHash)){
            return $this->getDojoHelper()
                    ->createGetResponse(null, false, $filter->getMessages());
        }
        
        $row = $this->getUserModel()->fetchRow(array(
            'loginName=?'=>$filter->loginName
        ));
        
        if($row==false){
            return $this->getDojoHelper()
                    ->createGetResponse(null, false);
        }
        
        $signKey = $this->getSession()->__get(self::LOGIN_PASSWORD_SIGN_HASH)?:sha1(rand(0,PHP_INT_MAX));
        
        $authAdapter = new Azf_Auth_Adapter_User($this->getUserModel());
        $authAdapter->setLoginName($loginName);
        $authAdapter->setSignKey($signKey);
        $authAdapter->setPassword($signedPasswordHash);
        
        $auth = Zend_Auth::getInstance();
        $authResult = $auth->authenticate($authAdapter);
        
        
        if($authResult->isValid()){
            Azf_Acl::getInstance()->reset();
            return $this->getDojoHelper()
                    ->createGetResponse();
        } else {
            return $this->getDojoHelper()
                    ->createGetResponse(null, false);
        }
    }

}
