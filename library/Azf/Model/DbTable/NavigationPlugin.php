<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_NavigationPlugin extends Zend_Db_Table_Abstract{
    protected $_name = "NavigationPlugin";
    protected $_primary = "id";
    
    
    /**
     *
     * @param int $navigationId
     * @param int $pluginId 
     * @param int $weight
     * @return int Primary key of inserted row
     */
    public function bind($navigationId, $pluginId, $weight = 200){
        return $this->insert(array(
            'navigationId'=>$navigationId,
            'pluginId'=>$pluginId,
            'weight'=>$weight
        ));
    }
    
    
    /**
     *
     * @param int $navigationId
     * @param int $pluginId 
     * @return int Number of affected rows
     */
    public function unbind($navigationId, $pluginId){
        return $this->delete(array(
            "navigationId=?"=>$navigationId,
            "pluginId=?"=>$pluginId
        ));
    }
    
    
    /**
     *
     * @param string $navigationId
     * @param string $regionId 
     * @return array()
     */
    public function findAllByNavigationAndRegion($navigationId, $regionId){
        $SQL = "(SELECT np.id, np.navigationId , np.pluginId, np.weight, p.name, p.description, p.`type`, 0 as disabled  FROM Navigation n
left JOIN NavigationPlugin np ON n.id = np.navigationId
left JOIN Plugin p ON np.pluginId=p.id
WHERE n.id=? AND p.region=?) 
UNION  (
SELECT null as id, null as navigationId, p.id as pluginId, 0 as weight, p.name, p.description, p.`type`, 1 as disabled FROM Plugin p
WHERE p.region=? AND p.id NOT IN (SELECT np.pluginId FROM Navigation n
left JOIN NavigationPlugin np ON n.id = np.navigationId
left JOIN Plugin p ON np.pluginId=p.id
WHERE n.id=? AND p.region=?)
)  ORDER BY disabled ASC, weight ASC, pluginId";
        return $this->getAdapter()->fetchAll($SQL, array(
            $navigationId,$regionId, $regionId,$navigationId,$regionId
        ));
    }
    
    
    /**
     *
     * @param int $id
     * @param int $weight
     * @return int
     */
    public function updateWeight($id, $weight){
        return $this->update(array('weight'=>$weight), array("id=?"=>$id));
    }
    
    
}
