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
        if(!$this->userModel){
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
    public function queryUsersMethod($query, $queryOptions, $staticOptions) {
        $model = $this->getUserModel();
        $dojoHelper = $this->getHelper("dojo");  /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */
        
        $start = $dojoHelper->getQueryOptionsStart($queryOptions);
        $count = $dojoHelper->getQueryOptionsCount($queryOptions);
        $returnCols = array('id','loginName','firstName','lastName','email');
        $searchLogin = $dojoHelper->getQueryStringParam($query, 'loginName');
        $searchEmail = $dojoHelper->getQueryStringParam($query,"email");
        
        
        $resultSet= $model->fetchByLoginNameAndEmail($searchLogin, $searchEmail, $count, $start);
        $rows = array();
        foreach($resultSet as $row){
            $rows[] = array_intersect_key($row->toArray(),  array_flip($returnCols));
        }
        return $dojoHelper->constructQueryResult($rows);
    }

}
