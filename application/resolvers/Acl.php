<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Acl extends Azf_Service_Lang_Resolver {

    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

    /**
     * 
     * @return Azf_Service_Lang_ResolverHelper_Dojo
     */
    public function getDojoHelper() {
        return $this->getHelper("dojo");
    }

    /**
     * @return Azf_Model_DbTable_UserAclGroup
     */
    public function getUserAclGroupModel() {
        return new Azf_Model_DbTable_UserAclGroup();
    }

    /**
     * @return Azf_Model_DbTable_AclAclGroup
     */
    public function getAclAclGroupModel() {
        return new Azf_Model_DbTable_AclAclGroup();
    }
    
    
    /**
     * 
     * @return \Azf_Model_DbTable_AclGroup
     */
    public function getAclGroupModel() {
        return new Azf_Model_DbTable_AclGroup();
    }

    /**
     * 
     * @param array $query
     * @param array $options
     * @param array $staticOptions
     */
    public function queryUserAclGroupMethod(array $query, array $options, array $staticOptions) {
        $count = $this->getDojoHelper()->getQueryOptionsCount($options);
        $start = $this->getDojoHelper()->getQueryOptionsStart($options);
        $groupFilter = Azf_Filter_Factory::get("aclGroup");
        $userFilter = Azf_Filter_Factory::get("user");
        $groupInputFilter = $groupFilter->getFilterInput(array(
            'name' => array(
                Azf_Filter_Abstract::REMOVE_VALIDATORS => array('stringLength'),
                Azf_Filter_Abstract::FIELD => 'aclGroupName',
                Azf_Filter_Abstract::ALLOW_EMPTY => true
            )
                ), array('name'));
        $userInputFilter = $userFilter->getFilterInput(array(
            'loginName' => array(
                Azf_Filter_Abstract::REMOVE_VALIDATORS => array('stringLength', 'db_NoRecordExists'),
                Azf_Filter_Abstract::FIELD => 'userLoginName',
                Azf_Filter_Abstract::ALLOW_EMPTY => true
            )
                ), array('loginName'));

        $groupInputFilter->setData($query);
        $userInputFilter->setData($query);

        if (!$groupInputFilter->isValid('aclGroupName') || !$userInputFilter->isValid('userLoginName')) {
            return $this->getDojoHelper()->constructQueryResult(array());
        } else {
            $groupName = $groupInputFilter->aclGroupName . "%";
            $userName = $userInputFilter->userLoginName . "%";
        }

        $result = $this->getUserAclGroupModel()->fetchAllByLoginNameAndGroupName($userName, $groupName, $count, $start);
        $totalNumOfResults = $this->getUserAclGroupModel()->countFetchAllByLoginNameAndGroupName($userName, $groupName);
        return $this->getDojoHelper()->constructQueryResult($result, $totalNumOfResults);
    }

    /**
     * 
     * @param array $userAclGroupQuerRecord
     * @return array
     */
    public function addUserToAclGroupMethod(array $userAclGroupQuerRecord) {
        $userFilter = Azf_Filter_Factory::get("user")->getFilterInput(array(
            'id' => array(
                Azf_Filter_Abstract::FIELD => 'userId'
            )
                ), array("id"));
        $gropuFilter = Azf_Filter_Factory::get("aclGroup")->getFilterInput(array(
            'id' => array(
                Azf_Filter_Abstract::FIELD => 'aclGroupId'
            )
                ), array('id'));

        $userFilter->setData($userAclGroupQuerRecord);
        $gropuFilter->setData($userAclGroupQuerRecord);

        if (!$userFilter->isValid('id') || !$gropuFilter->isValid('id')) {
            return $this->getDojoHelper()->createAddResponse(null, false);
        }

        $id = $this->getUserAclGroupModel()
                ->insert(array(
            'aclGroupId' => $gropuFilter->aclGroupId,
            'userId' => $userFilter->userId
                ));

        return $this->getDojoHelper()
                        ->createAddResponse($id);
    }

    /**
     * 
     * @param array $userAclGroupQuerRecord
     */
    public function putUserAclGroupGroupMethod(array $userAclGroupQuerRecord) {
        $userFilter = Azf_Filter_Factory::get("user")->getFilterInput(array(
            'id' => array(
                Azf_Filter_Abstract::FIELD => 'userId'
            )
                ), array("id"));
        $gropuFilter = Azf_Filter_Factory::get("aclGroup")->getFilterInput(array(
            'id' => array(
                Azf_Filter_Abstract::FIELD => 'aclGroupId'
            )
                ), array('id'));

        $userFilter->setData($userAclGroupQuerRecord);
        $gropuFilter->setData($userAclGroupQuerRecord);

        if (!$userFilter->isValid('userId') || !$gropuFilter->isValid('aclGroupId')) {
            return $this->getDojoHelper()->createAddResponse(null, false);
        }
        $userId = $userFilter->userId;
        $aclGroupId = $gropuFilter->aclGroupId;
        $memberOfGroup = isset($userAclGroupQuerRecord['memberOfGroup']) &&
                $userAclGroupQuerRecord['memberOfGroup'];

        if ($memberOfGroup) {
            $this->getUserAclGroupModel()
                    ->insert(array(
                        'userId' => $userId,
                        'aclGroupId' => $aclGroupId
                    ));
        } else {
            $this->getUserAclGroupModel()
                    ->delete(array(
                        'userId=?' => $userId,
                        'aclGroupId' => $aclGroupId
                    ));
        }

        return $this->getDojoHelper()
                        ->createPutResponse(TRUE);
    }
    
    

    /**
     * 
     * @param array $query
     * @param array $options
     * @param array $staticOptions
     * @return array
     */
    public function queryAclAclGroupMethod(array $query, array $options, array $staticOptions) {
        $dojoHelper = $this->getDojoHelper();

        $aclFilter = Azf_Filter_Factory::get("acl")
                ->getFilterInput(array(
            'resource' => array(
                Azf_Filter_Abstract::ALLOW_EMPTY => true,
                Azf_Filter_Abstract::FIELD => 'aclResource'
            )
                ), array('resource'));
        $aclGroupFilter = Azf_Filter_Factory::get("aclGroup")
                ->getFilterInput(array(
            'name' => array(
                Azf_Filter_Abstract::ALLOW_EMPTY => true,
                Azf_Filter_Abstract::FIELD => 'aclGroupName'
            )
                ));
        $aclFilter->setData($query);
        $aclGroupFilter->setData($query);

        if (!$aclFilter->isValid("aclResource") || !$aclGroupFilter->isValid("aclGroupName")) {
            return $dojoHelper->constructQueryResult(array(), 0);
        }
        $start = $dojoHelper->getQueryOptionsStart($options);
        $count = $dojoHelper->getQueryOptionsCount($options);

        $aclResource = $aclFilter->aclResource . "%";
        $aclGroupName = $aclGroupFilter->aclGroupName . "%";

        $results = $this->getAclAclGroupModel()->fetchAllByAclResourceAndGroupName($aclResource, $aclGroupName, $start, $count);
        $total = $this->getAclAclGroupModel()->countFetchAllByAclResourceAndGroupName($aclResource, $aclGroupName);
        return $dojoHelper->constructQueryResult($results,$total);
    }
    
    
    /**
     * 
     * @param array $record
     * @return array
     */
    public function putQueryAclAclGroupRecordMethod(array $record) {
        $aclIdFilter = Azf_Filter_Factory::get("acl")
                ->getFilterInput(array(
                    'id'=>array(
                    Azf_Filter_Abstract::FIELD=>'aclId'
                    )
                ),array('id'));
        $groupIdFilter = Azf_Filter_Factory::get("aclGroup")
                ->getFilterInput(array(
                    'id'=>array(
                    Azf_Filter_Abstract::FIELD=>'aclGroupId'
                    )
                ),array('id'));
        
        $aclIdFilter->setData($record);
        $groupIdFilter->setData($record);
        $isAssociatedValid = isset($record['associated']);
        
        if(!$aclIdFilter->isValid('aclId')||!$groupIdFilter->isValid('aclGroupId')||!$isAssociatedValid){
            return $this->getDojoHelper()
                    ->createPutResponse(null,false);
        }
        
        $associated=(boolean) $record['associated'];
        
        if($associated){
            $this->getAclAclGroupModel()
                    ->insert(array(
                        'aclId'=>$aclIdFilter->aclId,
                        'aclGroupId'=>$groupIdFilter->aclGroupId
                    ));
        } else {
            $this->getAclAclGroupModel()
                    ->delete(array(
                        'aclId=?'=>$aclIdFilter->aclId,
                        'aclGroupId=?'=>$groupIdFilter->aclGroupId
                    ));
        }
        
        return $this->getDojoHelper()->createPutResponse(true);
    }
    
    
    /**
     * 
     * @param array $query
     * @param array $options
     * @return array
     */
    public function queryAclGroupMethod(array $query,array $options) {
        $groupFilter = Azf_Filter_Factory::get("aclGroup")
                ->getFilterInput(array(
                    'name'=>array(
                    Azf_Filter_Abstract::ALLOW_EMPTY=>true
                    )
                ), array('name'));
        $groupFilter->setData($query);
        
        if(!$groupFilter->isValid('name')){
            return $this->getDojoHelper()
                    ->constructQueryResult(array());
        }
        
        $dojoHelper = $this->getDojoHelper();
        $start = $dojoHelper->getQueryOptionsStart($options);
        $count = $dojoHelper->getQueryOptionsCount($options);
        $name = $groupFilter->name."%";
        
        $results = $this->getAclGroupModel()
                ->fetchAllByName($name,$start,$count);
        $total = $this->getAclGroupModel()
                ->countFetchAllByName($name);
        
        return $dojoHelper->
        constructQueryResult($results, $total);
        
        
        
    }

}
