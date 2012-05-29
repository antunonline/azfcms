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
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $region
     * @param array $params
     * @return int Description
     */
    public function insertPlugin($name, $description, $type, $region, array $params) {
        $serializedParams = $this->_encode($params);

        return $this->insert(array(
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'region' => $region,
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
     * @param int $navigationId
     * @return array
     */
    public function findAllByNavigationid($navigationId) {
        $SQL = "select p.*, np.weight from Navigation n
JOIN NavigationPlugin np on n.id = np.navigationId
JOIN Plugin p ON np.pluginId = p.id
WHERE n.id = ?
ORDER BY p.region, np.weight";
        
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

}

