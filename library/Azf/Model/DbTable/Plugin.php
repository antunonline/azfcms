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
     * @param array $ps
     * @return array
     */
    protected function _recursiveCast(&$ps) {
        $ps = (array) $ps;
        foreach ($ps as &$p) {
            if (is_object($p)) {
                $this->_recursiveCast($p);
            }
        }
        return $ps;
    }

    /**
     * 
     * @param string $params
     * @return string
     */
    protected function _decode($params) {
        $decoded = json_decode($params);
        return $this->_recursiveCast($decoded);
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
            'weight'=>$plugin['weight']
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
        return $this->getAdapter()->fetchAll($SQL,array($pageTitleFilter,$pluginTitle,$pageTitleFilter,$pluginTitle));
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
        }
        
        return $result;
    }
    
    
    
    

}

