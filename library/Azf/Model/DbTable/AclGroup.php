<?php

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_AclGroup extends Zend_Db_Table_Abstract {

    protected $_name = "ACLGroup";
    protected $_primary = "id";

   
    /**
     * 
     * @param string $name
     * @return array
     */
    public function fetchAllByName($name,$start,$count){
        $SQL ="
SELECT 
    *
FROM
    ACLGroup a
WHERE
    a.name like ?
LIMIT
    ? , ?
    
";
        $stmt = $this->getAdapter()->prepare($SQL);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $start,PDO::PARAM_INT);
        $stmt->bindValue(3, $count,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * 
     * @param string $name
     * @param int $start
     * @param int $count
     * @return int
     */
    public function countFetchAllByName($name) {
$SQL = "
SELECT 
    count(a.id)
FROM
    ACLGroup a
WHERE
    a.name like '%'
";
    return $this->getAdapter()
            ->fetchOne($SQL,array($name));
    }
}

