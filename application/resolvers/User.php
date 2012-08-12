<?php

class Application_Resolver_User extends Azf_Service_Lang_Resolver {

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

    protected function isAllowed($namespaces, $parameters) {
        return true;
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

}
