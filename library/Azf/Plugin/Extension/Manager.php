<?php

/**
 * 
 *
 * @author antun
 */
class Azf_Plugin_Extension_Manager {
    
    
    /**
     *
     * @var Azf_Plugin_Descriptor
     */
    protected $_pluginDescriptor;

    /**
     *
     * @var Azf_Model_DbTable_Plugin
     */
    protected $_model;
    
    
    
    
    /**
     * 
     * @return Azf_Plugin_Descriptor
     */
    public function getPluginDescriptor() {
        if(!$this->_pluginDescriptor){
            $this->_pluginDescriptor = new Azf_Plugin_Descriptor();
        }
        
        return $this->_pluginDescriptor;
    }
    
    protected function _getPluginDescriptor($identifier) {
        return $this->getPluginDescriptor()->getExtensionPlugin($identifier);
    }
    
    
    /**
     * 
     * @param array $extensionPluginRecord
     * @return int
     */
    protected function _insertPluginRecord(array $extensionPluginRecord) {
        if(isset($extensionPluginRecord['module'])==false){
            $descriptor = $this->_getPluginDescriptor($extensionPluginRecord['type']);
            $extensionPluginRecord['module'] = $descriptor['module'];
        }
        return $this->getModel()->insertPlugin($extensionPluginRecord);
    }

    /**
     *
     * @param array $pluginRecord
     * @return int |null
     */
    public function setUp(array $pluginRecord) {
        $pluginId = $this->_insertPluginRecord($pluginRecord);
        
        $instance = $this->_getPluginInstance(null, null, $pluginId);
        /* @var $instance Azf_Plugin_Extension_Abstract */
        if ($instance) {
            $instance->setId($pluginId);
            try {
                $instance->setUp();
                $this->_saveParams($instance);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * 
     * @param int $pluginId
     */
    public function tearDown($pluginId) {
        $instance = $this->_getPluginInstance(null,null, $pluginId);


        if ($instance) {
            $instance->setId($pluginId);
            try {
                $instance->tearDown();
                $this->getModel()->deleteById($pluginId);
            } catch (Exception $exc) {
                
            }
        }
    }

    /**
     * 
     * @param int $navigationId
     * @return array
     */
    public function render($navigationId) {
        // Load plugin list
        $plugins = $this->getPluginDefinitions($navigationId);
        // Prepare response chunks array
        $responseChunks = array();
        // Prepare rendered array
        $rendered = array();

        foreach ($plugins as $plugin) {
            // Load instance
            $instance = $this->_getPluginInstance($plugin['type'], $plugin['module'], $plugin['id'], $plugin['params']);
            // Load region name
            $region = $plugin['region'];

            // If instance is loaded
            if ($instance) {
                $instance->setId($plugin['id']);
                try {
                    // Start output buffering
                    ob_start();
                    // Render plugin
                    $instance->render();
                    // Get rendering and clear buffer
                    $response = ob_get_clean();
                    // Create region withing response chunks arrray
                    if (isset($responseChunks[$region]) == false) {
                        $responseChunks[$region] = array();
                    }
                    // Add response to appropriate response chunk 
                    $responseChunks[$region][] = $response;
                } catch (Exception $exc) {
                    ob_get_clean();
                }
            }
        }

        // Set rendered array values
        foreach ($responseChunks as $region => $chunks) {
            $rendered[$region] = implode("", $chunks);
        }


        return $rendered;
    }

    
    /**
     *
     * @param Azf_Plugin_Extension_Abstract $instance 
     */
    protected function _saveParams(Azf_Plugin_Extension_Abstract $instance) {
        if ($instance->isParamsDirty()) {
            $params = $instance->getParams();
            $this->getModel()->setPluginParams($instance->getId(), $params);
            $instance->clearParamsDirty();
        }
    }

    /**
     * 
     * @param string|null $type
     * @param string|null $module
     * @param int|null $pluginId
     * @param array $pluginParams
     * @return null|Azf_Plugin_Extended_Abstract
     * 
     */
    protected function _getPluginInstance($type, $module, $pluginId, $pluginParams = null) {
        if (!$type  || !is_array($pluginParams)) {
            $pluginRecord = $this->getModel()->findById($pluginId);
        }

        if (is_array($pluginParams) == false) {
            $pluginParams = $pluginRecord['params'];
        }
        if (!$type) {
            $type = $pluginRecord['type'];
        }
        if(!$module){
           if($pluginRecord){
               $module = $pluginRecord['module'];
           } else {
               $descriptor = $this->_getPluginDescriptor($type);
               $module = $descriptor['module'];
           }
        }

        $instance = $this->_constructPlugin($module, $type, $pluginParams);
        /* @var $instance Azf_Plugin_Extension_Abstract */
        if($pluginId){
            $instance->setId($pluginId);
        }
        return $instance;
    }

    /**
     *
     * @param int $navigationId
     * @return array
     */
    public function getPluginDefinitions($navigationId) {
        return $this->getModel()->findAllByNavigationid($navigationId);
    }

    /**
     * @return Azf_Model_DbTable_Plugin
     */
    public function getModel() {
        if (empty($this->_model)) {
            $this->_initModel();
        }
        return $this->_model;
    }

    /**
     *
     * @return Azf_Model_DbTable_NavigationPlugin
     */
    public function getNavigationModel() {
        if (!$this->_navigationModel) {
            $this->_navigationModel = new Azf_Model_DbTable_NavigationPlugin();
        }
        return $this->_navigationModel;
    }

    /**
     *
     * @param Azf_Model_DbTable_NavigationPlugin $navigationModel 
     */
    public function setNavigationModel(Azf_Model_DbTable_NavigationPlugin $navigationModel) {
        $this->_navigationModel = $navigationModel;
    }

    /**
     * 
     */
    protected function _initModel() {
        $this->setModel(new Azf_Model_DbTable_Plugin());
    }

    /**
     * 
     * @param Azf_Model_DbTable_Plugin $model
     */
    public function setModel(Azf_Model_DbTable_Plugin $model) {
        $this->_model = $model;
    }

    /**
     * 
     * @param string $module
     * @param string $type
     */
    public function getClassName($module, $type) {
        
        if($module == "default"){
            $className = "Application_Plugin_Extension_" . ucfirst($type);
        } else {
            $className = ucfirst($module)."_Plugin_Extension_" . ucfirst($type);
        }
         
        return $className;
    }

    /**
     * 
     * @param string $module
     * @param string $type
     * @param array $pluginParams
     */
    public function _constructPlugin($module, $type, $pluginParams) {
        if($module != "default"){
            Azf_Bootstrap_Module::getInstance()->load($module);
        }
        $className = $this->getClassName($module, $type);
        return new $className($type,$module, $pluginParams);
    }

    /**
     *
     * @param int $pluginId
     * @param string|int $key
     * @param mixed $value
     * @return boolean
     */
    public function setValue($pluginId, $key, $value) {
        if (!$plugin = $this->_getPluginInstance(null, null, $pluginId))
            return false;

        $return = $plugin->setValue($key, $value);
        $this->_saveParams($plugin);
        return $return;
    }

    /**
     *
     * @param int|string $pluginId
     * @param mixed $values
     * @return boolean
     */
    public function setValues($pluginId, $values) {
        if (!$plugin = $this->_getPluginInstance(null, null, $pluginId))
            return false;

        /* @var $plugin Azf_Plugin_Extension_Abstract */
        $return = $plugin->setValues($values);
        $this->_saveParams($plugin);
        return $return;
    }

    /**
     *
     * @param string|int $pluginId
     * @param string|int $key
     * @return mixed
     */
    public function getValue($pluginId, $key) {
        if (!$plugin = $this->_getPluginInstance(null, null, $pluginId))
            return false;

        /* @var $plugin Azf_Plugin_Extension_Abstract */
        $return =  $plugin->getValue($key);
        $this->_saveParams($plugin);
        return $return;
    }

    /**
     *
     * @param int|string $pluginId
     * @return mixed
     */
    public function getValues($pluginId) {
        if (!$plugin = $this->_getPluginInstance(null, null, $pluginId))
            return false;

        /* @var $plugin Azf_Plugin_Extension_Abstract */
        $return = $plugin->getValues();
        $this->_saveParams($plugin);
        return $return;
    }

}

