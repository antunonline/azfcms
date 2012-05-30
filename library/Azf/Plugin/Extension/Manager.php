<?php

/**
 * 
 *
 * @author antun
 */
class Azf_Plugin_Extension_Manager {
    
    /**
     *
     * @var Azf_Model_DbTable_Plugin
     */
    protected $_model;

    /**
     * 
     * @param string $type
     * @param int $pluginId
     */
    public function setUp($type, $pluginId) {
        $instance = $this->_getPluginInstance($type, $pluginId);


        try {
            $instance->setUp();
        } catch (Exception $e) {
            
        }
    }

    /**
     * 
     * @param string $type
     * @param int $pluginId
     */
    public function tearDown($type, $pluginId) {
        $instance = $this->_getPluginInstance($type, $pluginId);


        if ($instance) {
            try {
                $instance->tearDown();
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
            $instance = $this->_getPluginInstance($plugin['type'], null, $plugin['params']);
            // Load region name
            $region = $plugin['region'];
            
            // If instance is loaded
            if ($instance) {
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
        foreach($responseChunks as $region => $chunks){
            $rendered[$region] = implode("", $chunks);
        }
        
        
        return $rendered;
    }

    /**
     * 
     * @param string $type
     * @param int|null $pluginId
     * @param array $pluginParams
     * @return Azf_Plugin_Extended_Abstract|null
     * 
     */
    protected function _getPluginInstance($type, $pluginId, $pluginParams = null) {
        if(is_array($pluginParams)==false){
            $pluginParams = $this->getModel()->getPluginParams($pluginId);
        }
        if(!is_array($pluginParams)){
            return null;
        }
        
        $instance = $this->_constructPlugin($type, $pluginParams);
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
        if(empty($this->_model)){
            $this->_initModel();
        }
        return $this->_model;
    }
    
    /**
     * 
     */
    protected function _initModel(){
        $this->setModel(new Azf_Model_DbTable_Plugin());
    }
    
    /**
     * 
     * @param Azf_Model_DbTable_Plugin $model
     */
    public function setModel(Azf_Model_DbTable_Plugin $model){
        $this->_model = $model;
    }

    /**
     * 
     * @param type $type
     */
    public function getClassName($type) {
        return "Application_Plugin_Extension_".  ucfirst($type);
    }

    /**
     * 
     * @param string $type
     * @param array $pluginParams
     */
    public function _constructPlugin($type, $pluginParams) {
        $className = $this->getClassName($type);
        return new $className($pluginParams);
    }

}

