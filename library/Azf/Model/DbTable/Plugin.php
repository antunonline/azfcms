<?php

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_Plugin extends Zend_Db_Table_Abstract {

    protected $_name = "Plugin";
    protected $_primary = "id";

    /**
     * 
     * @param array $ep
     * @return int Description
     */
    public function insertPlugin($ep) {
        $serializedParams = $this->_encode(isset($ep['params'])?$ep['params']:array());

        return $this->insert(array(
                    'name' => $ep['name'],
                    'description' => $ep['description'],
                    'module'=>$ep['module'],
                    'type' => $ep['type'],
                    'region' => $ep['region'],
                    'weight'=>$ep['weight'],
                    'params' => $serializedParams
                ));
    }

    /**
     * 
     * @param int $id
     * @return int  
     */
    public function deleteById($id) {
        return $this->delete(array("id=?" => $id));
    }

    /**
     * 
     * @param int $pluginId
     * @return array
     */
    public function getPluginParams($pluginId) {
        $params = $this->getAdapter()->fetchOne("SELECT params FROM Plugin WHERE id = ?", array($pluginId));
        return $this->_decode($params);
    }
    
    /**
     * 
     * @param int $pluginId
     * @param array $params
     * @return int
     */
    public function setPluginParams($pluginId, array $params){
        $stream = $this->_encode($params);
        $data = array(
            'params'=>$stream
        );
        $where = array('id=?'=>$pluginId);
        
        return $this->update($data, $where);
    }

    /**
     * 
     * @param int $navigationId
     * @return array
     */
    public function findAllByNavigationid($navigationId) {
        $SQL = "select p.*, np.weight from Navigation n
JOIN NavigationPlugin np on n.id = np.navigationId
JOIN Plugin p ON np.pluginId = p.id
WHERE n.id = ?
ORDER BY p.region, np.weight, p.weight";
        
        $rows = $this->getAdapter()->fetchAll($SQL,array($navigationId));
        foreach($rows as &$row){
            $row['params'] = $this->_decode($row['params']);
        }
        
        return $rows;
    }

    /**
     * 
     * @param string $params
     * @return string
     */
    protected function _decode($params) {
        return json_decode($params,true);
    }

    /**
     * 
     * @param array $params
     * @return string
     */
    protected function _encode(array $params) {
        return json_encode($params);
    }

    
    /**
     * 
     * @param array $plugin
     * @return boolean
     */
    public function updatePluginValues($plugin) {
        $data = array(
            'name'=>$plugin['name'],
            'description'=>$plugin['description'],
            'region'=>$plugin['region'],
            'weight'=>$plugin['weight'],
            'enabledByDefault'=>(int)$plugin['enabledByDefault']
        );
        $where = array(
            'id=?'=>$plugin['id']
        );
        
        return $this->update($data, $where);
    }

    
    /**
     *
     * @param int $pid
     * @return null|array
     */
    public function findById($pid) {
        $sql = "SELECT * FROM $this->_name WHERE id = ?";
        $row = $this->getAdapter()->fetchRow($sql,array($pid));
        if(!$row)
            return null;
        
        
        $row['params'] = $this->_decode($row['params']);
        return $row;
    }
    
    
    /**
     * 
     * @param string $pageTitleFilter
     * @param string $pluginTitle
     * @return array
     */
    public function fetchStatusMatrix($pageTitleFilter, $pluginTitle){
        $SQL  = "(SELECT DISTINCT
    n.id as navigationId,
    n.title as pageTitle,
    n.l as l,
    n.r as r,
    1 as enabled,
    np.id as navigationPluginId,
    np.weight as weight,
    p.id as pluginId,
    p.`name` as `pluginName`,
    p.region as pluginRegion,
    p.weight as pluginWeight
FROM
    Navigation n
JOIN
    NavigationPlugin np
ON
    n.id = np.navigationId
JOIN
    Plugin p
ON 
    np.pluginId = p.id
WHERE
    n.title like ?
AND
    p.name like ?
)
UNION
(SELECT DISTINCT
    n.id as navigationId,
    n.title as pageTitle,
    n.l as l,
    n.r as r,
    0 as enabled,
    0 as navigationPluginId,
    0 as weight,
    p.id as pluginId,
    p.`name` as `pluginName`,
    p.region as pluginRegion,
    p.weight as pluginWeight
FROM
    Navigation n
JOIN
    Plugin p
ON
    p.id NOT IN (SELECT np.pluginId FROM NavigationPlugin np WHERE np.navigationId = n.id)
WHERE
    n.title like ?
AND
    p.name like ?

) ORDER BY l,r, pluginRegion, enabled desc, weight ,pluginWeight;
";
        $stmt = $this->getAdapter()->prepare($SQL);
        $stmt->bindParam(1, $pageTitleFilter);
        $stmt->bindParam(3, $pageTitleFilter);
        $stmt->bindParam(2, $pluginTitle);
        $stmt->bindParam(4, $pluginTitle);
        $stmt->execute();
        
        /**
         * n.id as navigationId,
            n.title as pageTitle,
            n.l as l,
            n.r as r,
            1 as enabled,
            np.id as navigationPluginId,
            np.weight as weight,
            p.id as pluginId,
            p.`name` as `pluginName`,
            p.region as pluginRegion,
            p.weight as pluginWeight
         */
        $navigationId=$pageTitle=$l=$r=$navigationPluginId
        =$weight= $pluginId=$pluginName=$pluginRegion=$pluginWeight=
        $enabled=null;
        $stmt->bindColumn("navigationId", $navigationId,Zend_Db::PARAM_INT);
        $stmt->bindColumn("pageTitle", $pageTitle);
        $stmt->bindColumn("l", $l,Zend_Db::PARAM_INT);
        $stmt->bindColumn("r", $r,Zend_Db::PARAM_INT);
        $stmt->bindColumn("enabled", $enabled,Zend_Db::PARAM_INT);
        $stmt->bindColumn("navigationPluginId", $navigationPluginId,Zend_Db::PARAM_INT);
        $stmt->bindColumn("weight",$weight,Zend_Db::PARAM_INT);
        $stmt->bindColumn('pluginId',$pluginId,Zend_Db::PARAM_INT);
        $stmt->bindColumn("pluginName",$pluginName);
        $stmt->bindColumn("pluginRegion",$pluginRegion);
        $stmt->bindColumn("pluginWeight",$pluginWeight,Zend_Db::PARAM_INT);
        
        $rows = array();
        $i = 0;
        while($stmt->fetch(Zend_DB::FETCH_BOUND)){
            $rows[] = array(
                'rowId'=>$i,
                'navigationId'=>$navigationId,
                'pageTitle'=>$pageTitle,
                'l'=>$l,
                'r'=>$r,
                'enabled'=>(boolean)$enabled,
                'navigationPluginId'=>$navigationPluginId,
                'weight'=>$weight,
                'pluginId'=>$pluginId,
                'pluginName'=>$pluginName,
                'pluginRegion'=>$pluginRegion,
                'pluginWeight'=>$pluginWeight
            );
            $i++;
        }
        
        
        return $rows;
    }
    /**
     * 
     * @param int $pageId
     * @return array
     */
    public function fetchPageStatusMatrix($pageId){
        $SQL  = "(SELECT DISTINCT
    n.id as navigationId,
    1 as enabled,
    np.id as navigationPluginId,
    np.weight as weight,
    p.id as pluginId,
    p.`name` as `pluginName`,
    p.region as pluginRegion,
    p.weight as pluginWeight
FROM
    Navigation n
JOIN
    NavigationPlugin np
ON
    n.id = np.navigationId
JOIN
    Plugin p
ON 
    np.pluginId = p.id
WHERE
    n.id = ?
)
UNION
(SELECT DISTINCT
    n.id as navigationId,
    0 as enabled,
    0 as navigationPluginId,
    0 as weight,
    p.id as pluginId,
    p.`name` as `pluginName`,
    p.region as pluginRegion,
    p.weight as pluginWeight
FROM
    Navigation n
JOIN
    Plugin p
ON
    p.id NOT IN (SELECT np.pluginId FROM NavigationPlugin np WHERE np.navigationId = n.id)
WHERE
    n.id = ?

) ORDER BY l,r, pluginRegion, enabled desc, weight ,pluginWeight;
";
        return $this->getAdapter()->fetchAll($SQL,array($pageId, $pageId));
    }
    
    
    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $result = parent::fetchAll($where, $order, $count, $offset);
        /* @var $result Zend_Db_Table_Rowset_Abstract */
        foreach($result as $row){
            $row->params = $this->_decode($row->params);
            $row->enabledByDefault = $row->enabledByDefault=="0"?false:true;
        }
        
        return $result;
    }
    
    
    
    

}

