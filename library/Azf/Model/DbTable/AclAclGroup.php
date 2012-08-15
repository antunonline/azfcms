<?php

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_AclAclGroup extends Zend_Db_Table_Abstract {

    protected $_name = "Acl_AclGroup";
    protected $_primary = "id";

   public function fetchAllByAclResourceAndGroupName($resourceName, $groupName, $start, $count) {
       $SQL = "
(
SELECT
    a.id as aclId,
    a.resource as aclResource,
    g.id as aclGroupId,
    g.name as aclGroupName,
    1 as associated
FROM
    Acl a
JOIN 
    Acl_AclGroup ag
ON 
    a.id  = ag.aclId
JOIN
    ACLGroup g 
ON
    ag.aclGroupId = g.id
WHERE
    a.resource like :aclResource
AND
    g.name LIKE :aclGroupName

)
UNION
(
SELECT
    a.id as aclId,
    a.resource as aclResource,
    g.id as aclGroupId,
    g.name as aclGroupName,
    null as associated
FROM
    Acl a
JOIN
    ACLGroup g
ON
    g.id NOT IN (
    SELECT 
        g.id
    FROM
        ACLGroup g
    JOIN
        Acl_AclGroup ag
    ON
        g.id = ag.aclGroupId
    WHERE
        ag.aclId = a.id
    )
WHERE
    g.name like :aclGroupName
AND
    a.resource like :aclResource
) 
ORDER BY aclResource, aclGroupName           
LIMIT :start,:count
";
       $stmt = $this->getAdapter()->prepare($SQL);
       $stmt->bindParam("start", $start,PDO::PARAM_INT);
       $stmt->bindParam("count",$count,PDO::PARAM_INT);
       $stmt->bindParam("aclResource", $resourceName);
       $stmt->bindParam("aclGroupName", $groupName);
       $stmt->execute();
       
       return $stmt->fetchAll();
   }
   
   public function countFetchAllByAclResourceAndGroupName($resourceName, $groupName) {
       $SQL = "
SELECT
    a.count*g.count
FROM
(
SELECT 
    count(a.id) as count
FROM
    Acl a
WHERE
    a.resource like :aclResource
) a,
(
SELECT
    count(g.id) as count
FROM 
    ACLGroup g
WHERE
    g.`name` like :aclGroupName
) as g           
";
       return (int) $this->getAdapter()->fetchOne($SQL,array('aclResource'=>$resourceName,'aclGroupName'=>$groupName));
   }
}

