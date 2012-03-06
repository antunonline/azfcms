<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Navigation
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_Tree_Navigation extends Azf_Model_Tree_Abstract {

    const FIELD_STATIC = "final";
    const FIELD_DYNAMIC = 'abstract';

    protected $_name = "Navigation";
    protected $_primary = "id";
    
    protected $_staticParams = array();
    protected $_dynamicParams = array();
    protected $_pluginsParams = array();

    protected function createTemporaryTable() {
        $sql = <<<SQL
CREATE  TEMPORARY TABLE IF NOT EXISTS `TemporaryNavigation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parentId` INT UNSIGNED NULL ,
  `tid` INT UNSIGNED NOT NULL ,
  `l` INT UNSIGNED NULL ,
  `r` INT UNSIGNED NULL ,
  `disabled` INT UNSIGNED NOT NULL DEFAULT 1 ,
  `url` VARCHAR(125) NULL ,
  `final` MEDIUMTEXT NOT NULL ,
  `plugins` MEDIUMTEXT NOT NULL ,
  `abstract` MEDIUMTEXT NOT NULL);
SQL;
        $this->getAdapter()->query($sql);
    }

    protected function dropTemporaryTable() {
        $sql = "DROP TABLE TemporaryNavigation;";
        $this->getAdapter()->query($sql);
    }

    protected function insertNode($l, $r, $parentId, $value) {
        $value = (object) $value;
        $initialConfig = $this->_encodeConfig(array());
        $disabled = isset($value->disabled)?$value->disabled:1;
        $url  = isset($value->url)?$value->url:"/";
        $final = isset($value->final)?$value->final:"/";
        $plugins = isset($value->plugins)?$this->_encodeConfig($value->plugins):$initialConfig;
        $abstract = isset($value->abstract)?$this->_encodeConfig($value->abstract):$initialConfig;
        
        $record = array(
            'l' => $l,
            'r' => $r,
            'parentId' => $parentId,
            'tid' => $this->tid,
            'disabled' => $disabled,
            'url' => $url,
            'final' => $final,
            'plugins' => $plugins,
            'abstract' => $abstract
        );

        return Zend_Db_Table_Abstract::insert($record);
    }

    protected function mergeTemporaryTable() {
        $sql = <<<SQL
INSERT INTO Navigation SELECT * FROM TemporaryNavigation;   
SQL;
        $this->getAdapter()->query($sql);
    }

    protected function moveNodeIntoTemporaryTable($node) {
        $sql = <<<SQL
        INSERT INTO TemporaryNavigation SELECT * FROM Navigation n WHERE n.l >= ? AND n.r <= ?;
SQL;
        $this->getAdapter()->query($sql, array($node['l'], $node['r']));
    }

    protected function parseNode(array $r) {
        $r['childNodes'] = array();
        unset($r['tid']);

        return $r;
    }

    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?", array($id));
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
    public function update(array $data, $id) {
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
     * @param boolean $force
     * @return boolean
     * @throws RuntimeException
     */
    protected function _fetchConfiguration($id, $force = false) {
        $cacheKey = "i" . $id;
        if (isset($this->_staticParams[$cacheKey])&& $force == false) {
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

    protected function _saveCache($id) {
        $id = (int) $id;
        $cacheKey = "i" . $id;
        $record = array(
            'id' => $id,
            'plugins' => $this->_encodeConfig($this->_pluginsParams[$cacheKey]),
            self::FIELD_STATIC => $this->_encodeConfig($this->_staticParams[$cacheKey]),
            self::FIELD_DYNAMIC => $this->_encodeConfig($this->_dynamicParams[$cacheKey])
        );
        
        $this->update($record, $id);
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
        $field = (string) $field;
        $param = (string) $param;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        switch ($field) {
            case self::FIELD_STATIC:
                $this->_staticParams[$cacheKey][$param] = $value;
                break;
            case self::FIELD_DYNAMIC:
                $this->_dynamicParams[$cacheKey][$param] = $value;
                ;
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

        if (isset($params[$name])) {
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
        $field = (string)$field;
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
            return isset($this->_dynamicParams[$cacheKey][$name]) ? true : false;
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
        $cacheKey = "i" . $id;
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
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        if (isset($this->_pluginsParams[$cacheKey][$plugin])) {
            return $this->_pluginsParams[$cacheKey][$plugin];
        } else {
            return false;
        }
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin 
     * @return string
     */
    protected function _generatePluginName($id, $plugin){
        $parts = explode(":",$plugin);
        if(count($parts)==2){
            $name = $parts[0];
            $index = $parts[1];
        }else {
            $name = $plugin;
            $index = null;
        }
        
        if($name && !ctype_alnum($name)){
            throw new InvalidArgumentException("Plugin name can contain only alphanumeric characters");
        }
        
        if(!ctype_digit($index)){
            $pluginNames = $this->getPluginNames($id);
            $search = "$name:";
            $maxIndex = 0;
            for($i = 0, $count = count($pluginNames); $i < $count; $i++){
                $index = (int) ctype_digit(str_replace($search, "", $pluginNames[$i]));
                $maxIndex = $maxIndex < $index ? $index : $maxIndex;
            }
            $index = $maxIndex;
        }
        return "$name:$index";
    }

    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name
     * @param mixed $value
     * @return void 
     */
    protected function _setPluginParam($id, $plugin, $name, $value) {
        $id = (int) $id;
        $plugin = (string) $plugin;
        $name = (string) $name;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);
        
        $plugin = $this->_generatePluginName($id, $plugin);
        

        if (!isset($this->_pluginsParams[$cacheKey][$plugin])) {
            $this->_pluginsParams[$cacheKey][$plugin] = array();
        }

        $this->_pluginsParams[$cacheKey][$plugin][$name] = $value;

        $this->_saveCache($id);
        $this->_fetchConfiguration($id,true);
        return $plugin;
    }

    /**
     *
     * @param int $id
     * @param string $plugin
     * @param array $params
     * @return void 
     */
    protected function _setPluginParams($id, $plugin, array $params) {
        $id = (int) $id;
        $plugin = (string) $plugin;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);
        
        $plugin = $this->_generatePluginName($id, $plugin);
        

        $this->_pluginsParams[$cacheKey][$plugin] = $params;

        $this->_saveCache($id);
        $this->_fetchConfiguration($id,true);
        return $plugin;
    }

    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name 
     */
    protected function _deletePluginParam($id, $plugin, $name) {
        $id = (int) $id;
        $plugin = (string) $plugin;
        $name = (string) $name;
        $cacheKey = "i" . $id;
        $this->_fetchConfiguration($id);

        unset($this->_pluginsParams[$cacheKey][$plugin][$name]);
        $this->_saveCache($id);
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin 
     */
    protected function _deletePlugin($id, $plugin){
        $id = (int) $id;
        $plugin = (string) $plugin;
        $cacheKey = "i" . $id;
        
        $this->_fetchConfiguration($id);
        unset($this->_pluginsParams[$cacheKey][$plugin]);
        
        $this->_saveCache($id);
        $this->_fetchConfiguration($id, true);
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
        $params = $this->_getFieldParams($id, self::FIELD_STATIC);

        return isset($params[$name]) ? true : false;
    }

    /**
     * This method will return all static parameters encapsulated in stdClass
     * 
     * @param int $id
     * @return array
     * @throws InvalidArgumentException 
     */
    public function getStaticParams($id) {
        $params = $this->_getFieldParams($id, self::FIELD_STATIC);

        return $params;
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
    public function getDynamicParam($id, $name, $default=null) {
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
    public function getPluginParams($id, $plugin) {
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
    public function getPluginParam($id, $plugin, $name, $default = null) {
        $name = (string) $name;
        $params = $this->_getPluginParams($id, $plugin);

        return isset($params[$name]) ? $params[$name] : $default;
    }

    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name 
     * @return boolean
     */
    public function hasPluginParam($id, $plugin, $name) {
        $name = (string) $name;
        $params = $this->_getPluginParams($id, $plugin);

        return isset($params[$name]) ? true : false;
    }

    /**
     * 
     * @param int $id
     * @param string $plugin
     * @param string $name
     * @param mixed $value 
     * @return string
     */
    public function setPluginParam($id, $plugin, $name, $value) {
        return $this->_setPluginParam($id, $plugin, $name, $value);
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param array $params 
     * @return string
     */
    public function setPluginParams($id, $plugin, array $params){
        return $this->_setPluginParams($id, $plugin, $params);
    }

    
    /**
     *
     * @param int $id
     * @param string $plugin
     * @param string $name 
     */
    public function deletePluginParam($id, $plugin, $name) {
        $this->_deletePluginParam($id, $plugin, $name);
    }
    
    
    /**
     *
     * @param int $id
     * @param string $plugin 
     */
    public function deletePlugin($id, $plugin){
        $this->_deletePlugin($id,$plugin);
    }
    
    
    /**
     *
     * @param int $nodeId
     * @param int $userId
     * @return array
     */
    public function getBreadCrumbsMenu($nodeId, $userId){
        $return = $this->getAdapter()->fetchAll("call navigation_breadCrumbMenu (?,?);", array(
            $nodeId, $userId
        ));
        
        return $return;
    }
    
    
    /**
     *
     * @param int $userId
     * @return array
     */
    public function getRootMenu($userId){
        $return = $this->getAdapter()->fetchAll("call navigation_rootMenu (?);",array($userId));
        return $return;
    }
    
    
    /**
     * 
     * @param int $nodeId 
     * @param int $userId
     * @return arary
     */
    public function getContextualMenu($nodeId, $userId){
        $return = $this->getAdapter()->fetchAll("call navigation_contextMenu (?,?);",array($nodeId, $userId));
        return $return;
    }
    

}

