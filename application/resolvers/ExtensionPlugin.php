<?php

/**
 * Description of ExtensionPlugin
 *
 * @author antun
 */
class Application_Resolver_ExtensionPlugin extends Azf_Service_Lang_Resolver {
    
    /**
     *
     * @var Azf_Plugin_Extension_Manager
     */
    protected $manager;
    
    /**
     *
     * @var Azf_Model_DbTable_Plugin
     */
    protected $model;
    
    
    /**
     *
     * @var Azf_Model_DbTable_NavigationPlugin
     */
    protected $navigationPluginModel;
    
    /**
     * 
     * @return Azf_Plugin_Extension_Manager
     */
    public function getManager(){
        if(empty($this->manager)){
            $this->_initManager();
        }
        return $this->manager;
    }
    
    /**
     * 
     * @param Azf_Plugin_Extension_Manager $manager
     */
    public function setManager(Azf_Plugin_Extension_Manager $manager){
        $this->manager = $manager;
    }
    
    public function _initManager(){
        $this->setManager(new Azf_Plugin_Extension_Manager());
    }
    
    /**
     * 
     * @return Azf_Model_DbTable_Plugin
     */
    public function getModel() {
        if(empty($this->model)){
            $this->_initModel();
        }
        return $this->model;
    }

    public function setModel(Azf_Model_DbTable_Plugin $model) {
        $this->model = $model;
    }
    
    public function _initModel(){
        $this->setModel(Azf_Model_DbTable_Plugin());
    }
    
    /**
     * 
     * @return Azf_Model_DbTable_NavigationPlugin
     */
    public function getNavigationPluginModel() {
        if(empty($this->navigationPluginModel)){
            $this->_initNavigationPluginModel();
        }
        return $this->navigationPluginModel;
    }
    
    public function _initNavigationPluginModel(){
        $this->setNavigationPluginModel(new Azf_Model_DbTable_NavigationPlugin());
    }

    
    /**
     * 
     * @param Azf_Model_DbTable_NavigationPlugin $navigationPluginModel
     */
    public function setNavigationPluginModel(Azf_Model_DbTable_NavigationPlugin $navigationPluginModel) {
        $this->navigationPluginModel = $navigationPluginModel;
    }

    
        

    /**
     * @param int|null $navigationId
     * @param string $name
     * @param string $description
     * @param string $type
     * @param string $region
     * @param int $weight
     * @param boolean $enable
     * @return int
     */
    public function addExtensionPluginMethod($navigationId, $name, $description, $type, $region, $weight, $enable) {
        $pluginId = $this->getModel()->insertPlugin($name, $description, $type, $region);
        
        $this->getManager()->setUp($type, $pluginId);
        
        if($enable){
            $this->getNavigationPluginModel()
                    ->bind($navigationId, $pluginId, $weight);
        }
        return $pluginId;
    }

    /**
     * 
     * @param string $type
     * @param int $pluginId
     */
    public function removeExtensionPluginMethod($type, $pluginId) {
        $this->getManager()->tearDown($type, $pluginId);
        $this->getModel()->deleteById($pluginId);
    }

    /**
     * 
     * @param int $nodeId
     * @param int $pluginId
     * @param int $weight
     */
    public function enableExtensionPluginMethod($nodeId, $pluginId, $weight) {
        $this->getNavigationPluginModel()->bind($nodeId, $pluginId, $weight);
    }

    /**
     * 
     * @param int $nodeId
     * @param int $pluginId
     * @return null
     */
    public function disableExtensionPluginMethod($nodeId, $pluginId) {
        $this->getNavigationPluginModel()->unbind($nodeId, $pluginId);
    }

    /**
     * 
     * @param int $nodeId
     * @param string $region
     * @return array
     */
    public function getRegionExtensionPluginsMethod($nodeId, $region) {
        return $this->getNavigationPluginModel()->findAllByNavigationAndRegion($nodeId, $region);
    }
    
    public function setExtensionPluginValuesMethod($navigationId, $pluginId, $name, $description, $type, $weight, $enable){
        if($enable){
            $this->getNavigationPluginModel()->bind($navigationId, $pluginId,$weight);
        } else {
            $this->getNavigationPluginModel()->unbind($navigationId, $pluginId);
        }
        
        return $this->getModel()->updatePluginValues($pluginId,$name,$description, $type);
    }

    protected function _execute(array $namespaces, array $parameters) {
        if(count($namespaces)!=1){
            return false;
        }
        
        $method = array_shift($namespaces)."Method";
        
        
        if(method_exists($this, $method)){
            return call_user_method_array($method, $this, $parameters);
        } else {
            return false;
        }
    }
    
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

}
