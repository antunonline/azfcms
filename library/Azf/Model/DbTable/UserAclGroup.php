<?php

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_UserAclGroup extends Zend_Db_Table_Abstract {

    protected $_name = "User_ACLGroup";
    protected $_primary = "id";

   
    
    
    /**
     * 
     * @param string $userName
     * @param string $groupName
     * @param int $count
     * @param int $start
     * @return array
     */
    public function fetchAllByLoginNameAndGroupName($userName,$groupName,$count,$start){
        $SQL = "

(SELECT 
    u.id as userId,
    u.loginName as userLoginName,
    a.id as aclGroupId,
    a.`name` as aclGroupName,
    1 as memberOfGroup
FROM 
    User u
JOIN 
    User_ACLGroup ua
ON
    u.id = ua.userId
JOIN 
    ACLGroup a
ON
    a.id = ua.aclGroupId
WHERE
    u.loginName like :userLoginName 
AND
    a.name like :aclGroupName)
UNION
(
SELECT 
    u.id as userId,
    u.loginName as userLoginName,
    a.id as aclGroupId,
    a.`name` as aclGroupName,
    null as memberOfGroup
FROM 
    User u
JOIN 
    ACLGroup a
ON
    a.id NOT IN 
    (SELECT
        a1.id
    FROM 
        ACLGroup as a1
    JOIN
        User_ACLGroup ua
    ON
        a1.id= ua.aclGroupId
    WHERE
        ua.userId = u.id
    )
WHERE
    u.loginName like :userLoginName 
AND
    a.name like :aclGroupName
)
ORDER BY 
    userLoginName, 
    aclGroupName
LIMIT
    :start,:count
";
        
        $stmt = $this->getAdapter()->prepare($SQL);
        
        
        $stmt->bindParam("aclGroupName", $groupName);
        $stmt->bindParam("userLoginName", $userName);
        $stmt->bindParam("start", $start,Zend_Db::PARAM_INT);
        $stmt->bindParam("count", $count,Zend_Db::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    
    /**
     * 
     * @param string $userLoginName
     * @param string $aclGroupName
     * @return int
     */
    public function countFetchAllByLoginNameAndGroupName($userLoginName, $aclGroupName) {
        $SQL = "
SELECT
   u.count * a.count
FROM
	(
	SELECT 
		count(id) as count
	FROM
		User 
	WHERE 
		loginName like :userLoginName
	) as u,
	(
	SELECT
		count(id) as count
	FROM
		ACLGroup
	WHERE
		name like :aclGroupName
	) as a
";
        return (int) $this->getAdapter()->fetchOne($SQL,array(
            'userLoginName'=>$userLoginName,
            'aclGroupName'=>$aclGroupName
        ));
    }
    
}

