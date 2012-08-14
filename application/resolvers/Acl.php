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
        $totalNumOfResults = $this->getUserAclGroupModel()->getNumberOfJoinCombinations();
        return $this->getDojoHelper()->constructQueryResult($result,$totalNumOfResults);
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
        $aclGroupId= $gropuFilter->aclGroupId;
        $memberOfGroup = isset($userAclGroupQuerRecord['memberOfGroup'])&&
                    $userAclGroupQuerRecord['memberOfGroup'];
        
        if($memberOfGroup){
            $this->getUserAclGroupModel()
                    ->insert(array(
                        'userId'=>$userId,
                        'aclGroupId'=>$aclGroupId
                    ));
        } else {
            $this->getUserAclGroupModel()
                    ->delete(array(
                        'userId=?'=>$userId,
                        'aclGroupId'=>$aclGroupId
                    ));
        }
        
        return $this->getDojoHelper()
                ->createPutResponse(TRUE);
        
    }

}
