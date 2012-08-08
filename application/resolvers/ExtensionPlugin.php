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
    public function getManager() {
        if (empty($this->manager)) {
            $this->_initManager();
        }
        return $this->manager;
    }

    /**
     * 
     * @param Azf_Plugin_Extension_Manager $manager
     */
    public function setManager(Azf_Plugin_Extension_Manager $manager) {
        $this->manager = $manager;
    }

    public function _initManager() {
        $this->setManager(new Azf_Plugin_Extension_Manager());
    }

    /**
     * 
     * @return Azf_Model_DbTable_Plugin
     */
    public function getModel() {
        if (empty($this->model)) {
            $this->_initModel();
        }
        return $this->model;
    }

    public function setModel(Azf_Model_DbTable_Plugin $model) {
        $this->model = $model;
    }

    public function _initModel() {
        $this->setModel(new Azf_Model_DbTable_Plugin());
    }

    /**
     * 
     * @return Azf_Model_DbTable_NavigationPlugin
     */
    public function getNavigationPluginModel() {
        if (empty($this->navigationPluginModel)) {
            $this->_initNavigationPluginModel();
        }
        return $this->navigationPluginModel;
    }

    public function _initNavigationPluginModel() {
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
    public function addExtensionPluginMethod($extensionPlugin) {
        if(!is_array($extensionPlugin)){
            return false;
        }
        
        
//        $name, $description, $type, $region, $weight;
        $pluginId = $this->getModel()->insertPlugin($extensionPlugin);

        $this->getManager()->setUp($extensionPlugin['type'], $pluginId);
        return $pluginId;
    }

    public function setExtensionPluginValuesMethod($pluginRecord) {
        if(!is_array($pluginRecord)){
            return false;
        }
        return $this->getModel()->updatePluginValues($pluginRecord);
    }

    /**
     * 
     * @param int $pluginId
     */
    public function removeExtensionPluginMethod($pluginId) {
        $row = $this->getModel()->find($pluginId);
        if ($row->count() > 0) {
            $type = $row[0]->type;
            $this->getManager()->tearDown($type, $pluginId);
            $this->getModel()->deleteById($pluginId);
        }
    }

    /**
     * 
     * @param int $nodeId
     * @param int $pluginId
     * @param int $weight
     */
    public function enableExtensionPluginMethod($nodeId, $pluginId, $weight) {
        if ($this->getNavigationPluginModel()->isBinded($nodeId, $pluginId))
            return;

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

    /**
     * 
     * @param int $navigationId
     * @param string $region
     * @return array
     */
    public function findPluginsByNavigationAndRegionMethod($navigationId, $region) {
        $navigationId = (int) $navigationId;
        return $this->getNavigationPluginModel()->findAllByNavigationAndRegion($navigationId, $region);
    }

    /**
     *
     * @param int|string $pluginId
     * @param int|string  $key
     * @param mixed $value
     * @return boolean
     */
    protected function setExtensionValueMethod($pluginId, $key, $value) {
        return $this->getManager()->setValue($pluginId, $key, $value);
    }

    /**
     *
     * @param int|string $pluginId
     * @param mixed $values
     * @return boolean
     */
    protected function setExtensionValuesMethod($pluginId, $values) {
        return $this->getManager()->setValues($pluginId, $values);
    }

    /**
     *
     * @param int|string $pluginId
     * @param int|string  $key
     * @return mixed
     */
    protected function getExtensionValueMethod($pluginId, $key) {
        return $this->getManager()->getValue($pluginId, $key);
    }

    /**
     *
     * @param int|string $pluginId
     * @return mixed
     */
    protected function getExtensionValuesMethod($pluginId) {
        return $this->getManager()->getValues($pluginId);
    }

    public function getExtensionPluginStatusMatrixMethod($query = null, $args = array(), $staticQueryArgs = array()) {
        if (!is_array($staticQueryArgs) || !is_array($args)) {
            return array();
        }
        
        if(is_array($query) && isset($query['title']) && is_string($query['title'])){
            $pageTitleFilter = $query['title']."%";
        } else {
            $pageTitleFilter = "%";
        }
        
        if(is_array($query) && isset($query['pluginTitle']) && is_string($query['pluginTitle'])){
            $pluginTitle = $query['pluginTitle']."%";
        } else {
            $pluginTitle = "%";
        }

        $start = isset($args['start']) && (is_int($args['start']) || ctype_digit($args['start'])) ?
                $args['start'] : null;
        $count = isset($args['count']) && (is_int($args['count']) || ctype_digit($args['count'])) ?
                $args['count'] : null;

        if ($start === null || $count === null) {
            return array();
        }

        $rows = $this->getModel()->fetchStatusMatrix($pageTitleFilter,$pluginTitle);
        $returnRows = array();
        for ($i = $start, $newI = 0, $len = sizeof($rows); $i < $len && $i < ($start + $count); $i++, $newI++) {
            $rows[$i]['rowId'] = $i;
            $rows[$i]['enabled'] = (bool) (int) $rows[$i]['enabled'];
            $rows[$i]['weight'] = (int) $rows[$i]['weight'];
            $returnRows[$newI] = $rows[$i];
        }

        return array(
            'data' => $returnRows,
            'total' => sizeof($rows)
        );
    }

    public function testSetExtensionPluginStatusValidArguments($item) {
        if (!is_array($item)) {
            return false;
        }

        $requiredKeys = array('navigationId', 'enabled', 'navigationPluginId',
            'weight', 'pluginWeight', 'pluginId');
        for ($i = 0, $len = sizeof($requiredKeys); $i < $len; $i++) {
            $key = $requiredKeys[$i];
            $value = $item[$key];
            $isValid = false;
            
            if (array_key_exists($key, $item)) {
                
                $isInt = function($value){
                    return is_int($value)||(is_string($value)&&ctype_digit($value));
                };
                switch ($key) {
                    case "navigationId":
                        $isValid = $isInt($value);
                        break;
                    case "enabled":
                        $isValid = is_scalar($value);
                        break;
                    case "navigationPluginId":
                        $isValid = $isInt($value);
                        break;
                    case "weight":
                        $isValid = $isInt($value);
                        break;
                    case "pluginWeight":
                        $isValid = $isInt($value);
                        break;
                    case "pluginId":
                        $isValid = $isInt($value);
                        break;
                }
                
                
                if(!$isValid){
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function setExtensionPluginStatusMethod($item) {
        if (!$this->testSetExtensionPluginStatusValidArguments($item)) {
            return false;
        }
        
        $pluginModel = $this->getModel();
        $navigationPluginModel = $this->getNavigationPluginModel();

        if($item['enabled']==false){
            $navigationPluginModel->unbind($item['navigationId'], $item['pluginId']);
        } else {
            if(!$navigationPluginModel->isBinded($item['navigationId'], $item['pluginId'])){
                $navigationPluginModel->bind($item['navigationId'],$item['pluginId'],$item['weight']);
            }else {
                if($item['weight']!=0){
                    $navigationPluginModel->updateWeight($item['navigationPluginId'], $item['weight']);
                }
            }
        }
        
        if($item['pluginWeight']!=0){
            $pluginModel->update(array('weight'=>$item['pluginWeight']), array('id=?'=>$item['pluginId']));
        }
        return true;
    }
    
    
    public function bindPluginGloballyMethod($item){
        if(!$this->testSetExtensionPluginStatusValidArguments($item)){
            return false;
        } 
        
        $this->getNavigationPluginModel()->bindPluginGlobally($item['pluginId']);
    }
    
    public function unbindPluginGloballyMethod($item){
        if(!$this->testSetExtensionPluginStatusValidArguments($item)){
            return false;
        } 
        
        $this->getNavigationPluginModel()->unbindPluginGlobally($item['pluginId']);
    }
    
    
    
    public function getExtensionPluginsMethod($query =null, $options = array(), $staticOptions = array()){
        $dojoHelper = $this->getHelper("dojo");
        /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */

        
        $allPlugins = $this->getModel()->fetchAll();
        return $dojoHelper->sliceStoreResponse($allPlugins, $options);
    }

    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

}
