<?php


/**
 * 
 *
 * @author antun
 */
class Vrwhr_Model_DbTable_NewsCategory extends Zend_Db_Table_Abstract{
    protected $_name= "NewsCategory";
    protected $_primary = "id";
    
    
    /**
     * 
     * @param string $title
     * @param string $orderBy
     * @param string $order
     * @param int $start
     * @param int $count
     * @return array
     * 
     * @filter $title 
     * @filter $user
     * @orderBy $orderBy id|title|userId|created title
     * @order $order asc
     * @start $start 0
     * @count $count 25
     * @totalCall countSelectJoinUserWhereTitleAND
     */
    public function selectJoinUserWhereTitleAND($title, $orderBy, $order, $start, $count){
        $SQL = <<<EOL
SELECT 
    nc.id as id,
    nc.title as title,
    u.loginName as loginName
FROM
   NewsCategory nc
JOIN 
    User u
ON
   nc.userId = u.id
WHERE
   nc.title like :title
ORDER BY
`nc`.`$orderBy` $order
LIMIT
   :start, :count
EOL;
        
        $title = $title."%";
        $stmt = $this->getAdapter()->prepare($SQL);
        $stmt->bindParam("title", $title);
        $stmt->bindParam("start", $start, PDO::PARAM_INT);
        $stmt->bindParam("count", $count, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        return $results;
    }
    
    
    /**
     * 
     * @param type $title
     * @return type
     */
    public function countSelectJoinUserWhereTitleAND($title) {
        $SQL = <<<EOL
SELECT 
    count(title)    
FROM
   NewsCategory
WHERE 
    title like ?
EOL;
        
        return $this->getAdapter()->fetchOne($SQL, array($title."%"));
    }
}
