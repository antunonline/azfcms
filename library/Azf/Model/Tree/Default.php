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

    protected $_staticParams = array();
    protected $_dynamicParams = array();
    protected $_pluginsParams = array();

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
     * @param array $data
     * @param int $id 
     */
    public function update(array $data, $id){
        $id = (int) $id;
        parent::update($data, "$this->_primary = $id");
    }

    /**
     *
     * @param mixed $config
     * @return string
     */
    protected function _encodeConfig($config) {
        return json_encode($config);
    }

    /**
     *
     * @param string $config
     * @return array
     */
    protected function _decodeConfig($config) {
        return (array) json_decode($config);
    }

    /**
     *
     * @param void $id
     * @return boolean
     * @throws RuntimeException
     */
    protected function _fetchConfiguration($id) {
        $cacheKey = "i" . $id;
        if (isset($this->_staticParams[$cacheKey])) {
            return true;
        }

        $stmt = $this->getAdapter()->prepare("call fetchConfiguration(?)");
        $stmt->execute(array($id));
        $row = $stmt->fetch();

        if ($row) {
            $this->_staticParams[$cacheKey] = array();
            $this->_dynamicParams[$cacheKey] = array();
            $this->_pluginsParams[$cacheKey] = array();
            $plugins = array();

            // Store dynamic config
            $this->_staticParams[$cacheKey] = $this->_decodeConfig($row[self::FIELD_STATIC]);
            do {
                $tmpDynamic = $this->_decodeConfig($row[self::FIELD_DYNAMIC]);
                $this->_dynamicParams[$cacheKey]+=$tmpDynamic;

                $tmpPlugins = $this->_decodeConfig($row['plugins']);
                foreach ($tmpPlugins as $pluginName => $pluginArray) {
                    $pluginArray = (array) $pluginArray;

                    if (isset($plugins[$pluginName])) {
                        $plugins[$pluginName] += $pluginArray;
                    } else {
                        $plugins[$pluginName] = $pluginArray;
                    }
                }
            } while ($row = $stmt->fetch());
            $this->_pluginsParams[$cacheKey] = $plugins;
        } else {
            throw new RuntimeException("Navigation record with ID $id does not exits");
        }
        return true;
    }
    
    protected function _saveCache($id){
        $id = (int) $id;
        $cacheKey = "i".$id;
        $record = array(
            'id'=>$id,
            'plugins'=>$this->_encodeConfig($this->_pluginsParams[$cacheKey]),
            self::FIELD_STATIC => $this->_encodeConfig($this->_staticParams[$cacheKey]),
            self::FIELD_DYNAMIC => $this->_encodeConfig($this->_dynamicParams[$cacheKey])
        );
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
        $id = (int)$id;
        $field = (string) $field;
        $param = (string) $param;
        $cacheKey = "i".$id;
        $this->_fetchConfiguration($id);
        
        switch($field){
            case self::FIELD_STATIC:
                $this->_staticParams[$cacheKey][$param] = $value;
                break;
            case self::FIELD_DYNAMIC:
                $this->_dynamicParams[$cacheKey][$param] = $value;;
                break;
            default:
                throw new InvalidArgumentException("Invalid field name specified");
                break;
        }
        
        $this->_saveCache($id);
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
        $params = $this->_getFieldParams($id, $field);
        
        if(isset($params[$name])){
            return $params[$name];
        } else {
            return $default;
        }
    }

    /**
     *
     * @param int $id
     * @param string $field
     * @return array
     * @throws InvalidArgumentException 
     */
    protected function _getFieldParams($id, $field) {
        $id = (int) $id;
        $name = (string) $name;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        if ($field == self::FIELD_STATIC) {
            $params = $this->_staticParams[$cacheKey];
        } else if ($field == self::FIELD_DYNAMIC) {
            $params = $this->_dynamicParams[$cacheKey];
        } else {
            throw new InvalidArgumentException("Invalid field name specified \"$field\"");
        }
        
        return $params;
    }

    /**
     *
     * @param int $id
     * @param string $field
     * @param string $name
     * @return array
     * @throws InvalidArgumentException 
     */
    protected function _hasFieldParam($id, $field, $name) {
        $id = (int) $id;
        $field = (string) $field;
        $name = (string) $name;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        if ($field == self::FIELD_STATIC) {
            return isset($this->_staticParams[$cacheKey][$name]) ? true : false;
        } else if ($field == self::FIELD_DYNAMIC) {
            return isset($this->_dynamicParams[$cacheKey][$name]) ? true: false;
        } else {
            throw new InvalidArgumentException("Invalid field name specified \"$field\"");
        }
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
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        if ($field == self::FIELD_STATIC) {
            unset($this->_staticParams[$cacheKey][$name]);
        } else if ($field == self::FIELD_DYNAMIC) {
            unset($this->_dynamicParams[$cacheKey][$name]);
        } else {
            throw new InvalidArgumentException("Invalid field name specified \"$field\"");
        }
        
        $this->_saveCache($id);
    }

    /**
     *
     * @param type $id 
     * @return string
     */
    protected function _getPluginNames($id) {
        $id = (int) $id;
        $cacheKey = "i".$id;
        $this->_fetchConfiguration($id);
        
        return array_keys($this->_pluginsParams[$cacheKey]);
    }

    /**
     *
     * @param int $id
     * @param string $plugin 
     * @return array|false
     */
    protected function _getPluginParams($id, $plugin) {
        $id = (int) $id;
        $plugin = (string) $plugin;
        $cacheKey = "i".$id;
        $this->_fetchConfiguration($id);
        
        if(isset($this->_pluginsParams[$cacheKey][$plugin])){
            return $this->_pluginsParams[$cacheKey][$plugin];
        }else {
            return false;
        }
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name
     * @param mixed $value
     * @return void 
     */
    protected function _setPluginParams($id, $plugin, $name, $value){
        $id = (int) $id;
        $plugin = (string) $plugin;
        $name = (string) $name;
        $cacheKey = "i".$id;
        $this->_fetchConfiguration($id);
        
        if(!isset($this->_pluginsParams[$cacheKey][$plugin])){
            $this->_pluginsParams[$cacheKey][$plugin] = array();
        }
        
        $this->_pluginsParams[$cacheKey][$plugin][$name] = $value;
        
        $this->_saveCache($id);
        
    }
    
    protected function _deletePluginParam($id, $plugin, $name){
        $id = (int) $id;
        $plugin = (string) $plugin;
        $name = (string) $name;
        $cacheKey = "i".$id;
        $this->_fetchConfiguration($id);
        
        unset($this->_pluginsParams[$cacheKey][$plugin][$name]);
        $this->_saveCache($id);
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
        $params = $this->_getFieldParams($id);

        return isset($params[$name]) ? true : false;
    }

    /**
     * This method will return all static parameters encapsulated in stdClass
     * 
     * @param int $id
     * @return stdClass
     * @throws InvalidArgumentException 
     */
    public function getStaticParams($id) {
        $params = $this->_getFieldParams($id);

        return (object) $params;
    }

    /**
     *
     * @param int $id
     * @param string $name
     * @param mixed $value 
     */
    public function setDynamicParam($id, $name, $value) {
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
        return $this->_getFieldParam($id, self::FIELD_DYNAMIC, $name, $default);
    }

    /**
     *
     * @param int $id
     * @param string $name
     * @return boolean 
     */
    public function hasDynamicParam($id, $name) {
        return $this->_hasFieldParam($id, self::FIELD_DYNAMIC, $name);
    }

    /**
     *
     * @param int $id
     * @param string $name 
     */
    public function deleteDynamicParam($id, $name) {
        $this->_deleteFieldParam($id, self::FIELD_DYNAMIC, $name);
    }

    /**
     *
     * @param int $id 
     * @return stdClass
     */
    public function getDynamicParams($id) {
        return $this->_getFieldParams($id, self::FIELD_DYNAMIC);
    }

    /**
     * This method will return names of all available plugins associated 
     * with the id menu node.
     * 
     *
     * @param type $id 
     * @return array
     */
    public function getPluginNames($id) {
        return $this->_getPluginNames($id);
    }
    
    
    /**
     * This method will return plugin parameters as as array
     * @param int $id
     * @param string $plugin
     * @return array|false
     */
    public function getPluginParams($id, $plugin){
        return $this->_getPluginParams($id, $plugin);
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name
     * @param mixed $default 
     * @return mixed
     */
    public function getPluginParam($id, $plugin, $name, $default){
        $name = (string) $name;
        $params = $this->_getPluginParams($id, $plugin);
        
        isset($params[$name])? $params[$name] : $default;
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name 
     * @return boolean
     */
    public function hasPluginParam($id, $plugin, $name){
        $name = (string) $name;
        $params = $this->_getPluginParams($id, $plugin);
        
        isset($params[$name])? true : false;
    }
    
    
    /**
     * 
     * @param int $id
     * @param string $plugin
     * @param string $name
     * @param mixed $value 
     */
    public function setPluginParam($id, $plugin, $name, $value){
        $this->_setPluginParams($id, $plugin, $name, $value);
    }
    
    
    public function deletePluginParam($id, $plugin, $name){
        $this->_deletePluginParam($id, $plugin, $name);
    }
    

}