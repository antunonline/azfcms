<?php

class Application_Resolver_Navigation extends Azf_Service_Lang_Resolver {

    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigation;
    
    /**
     *
     * @var Azf_Plugin_Descriptor
     */
    protected $pluginDescriptor;

    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigation() {
        return $this->navigation;
    }

    /**
     *
     * @param Azf_Model_Tree_Navigation $navigation 
     */
    public function setNavigation(Azf_Model_Tree_Navigation $navigation) {
        $this->navigation = $navigation;
    }
    
    /**
     * 
     */
    protected function _initPluginDescriptor() {
        $this->setPluginDescriptor(new Azf_Plugin_Descriptor());
    }
    
    /**
     *
     * @return Azf_Plugin_Descriptor 
     */
    public function getPluginDescriptor() {
        if(empty($this->pluginDescriptor)){
            $this->_initPluginDescriptor();
        }
        return $this->pluginDescriptor;
    }

    /**
     *
     * @param Azf_Plugin_Descriptor $pluginDescriptor 
     */
    public function setPluginDescriptor($pluginDescriptor) {
        $this->pluginDescriptor = $pluginDescriptor;
    }

    public function initialize() {
        parent::initialize();
        $this->setNavigation(Zend_Registry::get("navigationModel"));
    }

    /**
     *
     * @param array $namespaces
     * @param array $parameters
     * @return mixed 
     */
    protected function _execute(array $namespaces, array $parameters) {
        if (sizeof($namespaces) != 1) {
            return null;
        }

        /**
         * Pop method name 
         */
        $method = array_shift($namespaces);
        // Build override method name
        $overrideMethod = 'override'.ucfirst($method);
        
        
        /**
         * If override method exists 
         */
        if(method_exists($this, $overrideMethod)){
            return call_user_method_array($overrideMethod, $this, $parameters);
        }
        // If Model method exists
        else if (method_exists($this->getNavigation(), $method)) {
            return call_user_method_array($method, $this->getNavigation(), $parameters);
            // Otherwise return null
        } else {
            return null;
        }
    }
    
    /**
     * Start override method declarations 
     */

    /**
     *
     * @return array
     */
    public function overrideGetContentPlugins() {
        $pd = $this->getPluginDescriptor();
        return $pd->getContentPlugins();
    }

    /**
     *
     * @return array
     */
    public function overrideGetExtensionPlugins() {
        $pd = $this->getPluginDescriptor();
        return $pd->getExtensionPlugins();
    }
    
    
    /**
     *
     * @param int $intoId
     * @param array $value
     * @param string $pluginIdentifier
     * @return id 
     */
    public function overrideInsertInto($intoId, $value, $pluginIdentifier){
        $navigation = $this->getNavigation();
        $newId = $navigation->insertInto($intoId, array(
            'title'=>$value['title'],
            'url'=>isset($value['url'])?$value['url']:urlencode($value['title'])
        ));
        
        // Prepare page
        $this->_prepareInseredPage($newId,$pluginIdentifier);
        
        $navigation->setStaticParam($newId, 'pluginIdentifier', $pluginIdentifier);
        return $newId;
    }
    
    /**
     * This method will prepare newly insered by 
     */
    public function _prepareInseredPage($id,$pluginIdentifier){
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);
        $response = new Zend_Controller_Response_Http();
        
        // Initialize front controller
        $fc = Zend_Controller_Front::getInstance();
        $fc->throwExceptions(true);
        $application = $GLOBALS['application'];
        /* @var $application Zend_Application */
        
        $fc->setParam('bootstrap',$application->getBootstrap());
        // Add route 
        $r = $fc->getRouter();
        /* @var $r Zend_Controller_Router_Rewrite */
        $routeDefaults = array(
            'id'=>$id,
            'action'=>'installpage'
        )+$plugin;
        $r->addRoute("default", new Zend_Controller_Router_Route_Regex(".*",$routeDefaults));
        
        // Dispatch request
        $fc->dispatch(null,$response);
        // Prepare controller
        
    }
    
    
    protected function isAllowed($namespaces, $parameters) {
        // TODO fix me
        return true;
    }


}
