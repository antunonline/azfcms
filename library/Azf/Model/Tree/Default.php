<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TreeModel
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_Tree_Default extends Azf_Model_Tree_Abstract {
    
    const FIELD_STATIC = "final";
    const FIELD_DYNAMIC = 'abstract';
    
    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?", array($id));
    }

    protected function insertNode($l, $r, $parentId, $value) {
        return $this->insert(array(
                    'l' => $l,
                    'r' => $r,
                    'parentId' => $parentId,
                    'tid' => $this->tid,
                    'value' => $value
                ));
    }

    protected function createTemporaryTable() {
        $temporaryTreeTableDDLSQL = "CREATE TEMPORARY TABLE TemporaryTreeTable( id INT UNSIGNED NOT NULL, parentId INT UNSIGNED, l INT UNSIGNED, r INT UNSIGNED, tid INT UNSIGNED, value VARCHAR(255));";
        $this->getAdapter()->query($temporaryTreeTableDDLSQL);
    }

    protected function moveNodeIntoTemporaryTable($node) {

        $moveNodeIntoTemporaryTableSQL = "INSERT INTO TemporaryTreeTable (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($moveNodeIntoTemporaryTableSQL, array($node['l'], $node['r'], $this->tid));
    }

    protected function mergeTemporaryTable() {
        $mergeTemporaryTableSQL = "INSERT INTO $this->_name (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM TemporaryTreeTable;";
        $this->getAdapter()->query($mergeTemporaryTableSQL);
    }

    protected function dropTemporaryTable() {
        $dropTemporaryTableSQL = "DROP TABLE TemporaryTreeTable;";
        $this->getAdapter()->query($dropTemporaryTableSQL);
    }

    protected function parseNode(array $r) {
        $r['childNodes'] = array();
        unset($r['tid']);

        return $r;
    }

    protected function getTableName() {
        return "Tree";
    }

    /**
     * This method will check value type, and will throw
     * an exception if the value could not json serialized.
     * 
     * @param mixed $value 
     */
    public function isStorableValue($value) {
        if (!is_scalar($value)) {
            if (!is_array($value) && !$value instanceof stdClass) {
                throw new InvalidArgumentException("Value could not be serialized, only allowed serializable object are the ones that are instatiated from stdClass.");
            }
        }
    }

    /**
     * This method will disable current and all sibling nodes
     * @param int $nodeId 
     * @return void
     */
    public function disable($nodeId) {
        $this->getAdapter()->query("call navigation_disable (?);", array($nodeId));
    }

    /**
     * This method will enable current and all sibling nodes
     *
     * @param int $nodeId 
     */
    public function enable($nodeId) {
        $this->getAdapter()->query("call navigation_enable (?);", array($nodeId));
    }

    /**
     * This method will return menu structure which complies to users ACL rights.
     * Menus to which the user has no access will not be included.
     *
     * @param int $userId
     * @return array
     */
    public function getCompleteMenu($userId) {
        $stmt = $this->getAdapter()->prepare("call navigation_completeUserMenu (?);");
        $stmt->execute(array($userId));

        return $this->_parseTree($stmt);
    }

    
    /**
     *
     * @param int $id
     * @param string $field
     * @param string $param
     * @param mixed $value
     * @return void
     * @throws InvalidArgumentException 
     */
    protected function _setFieldParam($id, $field, $param, $value) {
        $id = (int) $id;
        $param = (string) $param;
        $this->isStorableValue($value);
        
        $record = $this->find($id);
        if (!$record) {
            throw new InvalidArgumentException("Record with id $id does not exist");
        }

        $static = json_decode($record[$field]);
        $static->$param = $value;
        $record[$field] = json_encode($static);

        $this->update($static, "id=$id");
    }

    /**
     *
     * @param int $id
     * @param string $field
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @throws InvalidArgumentException 
     */
    protected function _getFieldParam($id, $field, $name, $default) {
        $id = (int) $id;
        $name = (string) $name;
        $record = $this->find($id);
        if (!$record) {
            throw new InvalidArgumentException("Record with id $id does not exist");
        }

        $static = json_decode($record[$field]);
        return $static->$name? : $default;
    }
    
    
    /**
     *
     * @param int $id
     * @return array
     * @throws InvalidArgumentException 
     */
    protected function _getStaticParams($id){
        $id = (int) $id;
        $field = self::FIELD_STATIC;
        $record = $this->find($id);
        if (!$record) {
            throw new InvalidArgumentException("Record with id $id does not exist");
        }

        $params = json_decode($record[$field]);
        return (array)$params;
    }
    
    
    /**
     *
     * @param int $id
     * @param string $field
     * @param string $name
     * @throws InvalidArgumentException 
     */
    protected function _deleteFieldParam($id, $field, $name) {
        $id = (int) $id;
        $name = (string) $name;
        $record = $this->find($id);
        if (!$record) {
            throw new InvalidArgumentException("Record with id $id does not exist");
        }

        $static = json_decode($record[$field]);
        unset($static->$name);
        $record[$field] = json_encode($static);

        $this->update($static, "id=$id");
    }
    
    

    
    /**
     *
     * @param int $id
     * @return array
     * @throws InvalidArgumentException 
     */
    protected function _getDynamicParams($id){
        $id = (int) $id;
        $name = (int) $name;
        
        $stmt = $this->getAdapter()->prepare("call navigation_dynamicParams (?);");
        $stmt->execute(array($id));
        
        $dynamic = array();
        while($row = $stmt->fetch()){
            $hasRecords = true;
            $tmpDynamic = (array) json_decode($row['abstract']);
            $dynamic += $tmpDynamic;
        }
        if(!isset($hasRecords)){
            throw new InvalidArgumentException("Record with id $id does not exist");
        }
        
        return $dynamic;
    }
    
    
    /**
     *
     * @param type $id 
     * @return string
     */
    protected function _getPlugins($id){
        
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin 
     * @return stdClass
     */
    protected function _getPluginParams($id, $plugin){
        
    }

    /**
     *
     * @param int $id
     * @param type $name
     * @param type $value
     * @throws InvalidArgumentException 
     */
    public function setStaticParam($id, $name, $value) {
        $this->_setFieldParam($id, "final", $name, $value);
    }

    /**
     *
     * @param int $id
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function getStaticParam($id, $name, $default = null) {
        return $this->_getFieldParam($id, self::FIELD_STATIC, $name, $default);
    }

    /**
     * This method will unset property from the static configuration object
     * @param int $id
     * @param string $name
     * @throws InvalidArgumentException 
     */
    public function deleteStaticParam($id, $name) {
        $this->_deleteFieldParam($id, self::FIELD_STATIC, $name);
    }

    /**
     *
     * @param int $id
     * @param string $name 
     * @return boolean
     */
    public function hasStaticParam($id, $name) {
        $name = (string) $name;
        $params = $this->_getStaticParams($id);

        return isset($params[$name])?true:false;
    }

    /**
     * This method will return all static parameters encapsulated in stdClass
     * 
     * @param int $id
     * @return stdClass
     * @throws InvalidArgumentException 
     */
    public function getStaticParams($id) {
        $params = $this->_getStaticParams($id);

        return (object) $params;
    }
    
    
    /**
     *
     * @param int $id
     * @param string $name
     * @param mixed $value 
     */
    public function setDynamicParam($id, $name, $value){
        $this->_setFieldParam($id, self::FIELD_DYNAMIC, $name, $value);
    }
    
    /**
     *
     * @param int $id
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function getDynamicParam($id, $name, $default) {
        $dynamic = $this->_getDynamicParams($id);
        
        return isset($dynamic[$name])?$dynamic[$name]:$default;
    }
    
    
    /**
     *
     * @param int $id
     * @param string $name
     * @return boolean 
     */
    public function hasDynamicParam($id, $name){
        $dynamic = $this->_getDynamicParams($id);
        
        return isset($dynamic[$name])?true:false;
    }
    
    
    /**
     *
     * @param int $id
     * @param string $name 
     */
    public function deleteDynamicParam($id, $name){
        $this->_deleteFieldParam($id, self::FIELD_DYNAMIC, $name);
    }
    
    
    /**
     *
     * @param int $id 
     * @return stdClass
     */
    public function getDynamicParams($id){
        return (object) $this->_getDynamicParams($id);
    }
    
    
    /**
     * This method will return names of all available plugins associated 
     * with the id menu node.
     * 
     *
     * @param type $id 
     * @return array
     */
    public function getPlugins($id){
        
    }

}