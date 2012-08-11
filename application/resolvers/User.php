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
        $returnCols = array('id', 'loginName', 'firstName', 'lastName', 'email');
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
            return $filterInput->getErrors();
        }
        $newUser = $filterInput->getEscaped();
        $this->getUserModel()->insert($newUser);
        return true;
    }

    public function saveUserMethod(array $user) {
        $filter = Azf_Filter_Factory::get("user");
        $filterInput = $filter->getFilterInput(
                array(
                    'password' => array(
                        Azf_Filter_Abstract::REMOVE => true
                    ),
                    'loginName' => array(
                        Azf_Filter_Abstract::REMOVE_VALIDATORS => array(
                            'db_NoRecordExists'
                        )
                    )
                )
        );
        $filterInput->setData($user);

        if (!$filterInput->isValid()) {
            return $filterInput->getMessages();
        }

        $newUser = $filterInput->getEscaped();
        unset($newUser['id']);
        $id = $filterInput->id;

        $this->getUserModel()->update($newUser, array('id=?' => $id));
        return true;
    }

}
