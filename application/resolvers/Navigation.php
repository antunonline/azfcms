<?php

class Application_Resolver_Navigation extends Azf_Service_Lang_Resolver {
    
    const PARAM_CONTENT_PLUGIN_IDENTIFIER = "pluginIdentifier";

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
     * @var Zend_Controller_Front
     */
    protected $frontController;

    /**
     *
     * @var Azf_Controller_Router_Route_Fixed
     */
    protected $route;

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
        if (empty($this->pluginDescriptor)) {
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

    /**
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController() {
        if (empty($this->frontController)) {
            $fc = $this->_getFrontController();
            $this->setFrontController($fc);
        }
        return $this->frontController;
    }

    /**
     *
     * @param Zend_Controller_Front $frontController 
     */
    public function setFrontController(Zend_Controller_Front $frontController) {
        $this->frontController = $frontController;
    }

    /**
     *
     * @return Azf_Controller_Router_Route_Fixed 
     */
    public function getRoute() {
        if (empty($this->route)) {
            $route = $this->getFrontController()->getRouter()->getRoute("default");
            $this->setRoute($route);
        }
        return $this->route;
    }

    /**
     *
     * @param Azf_Controller_Router_Route_Fixed $route 
     */
    public function setRoute(Azf_Controller_Router_Route_Fixed $route) {
        $this->route = $route;
    }

    public function initialize() {
        parent::initialize();
        $this->setNavigation(Zend_Registry::get("navigationModel"));
    }
    
    
    /**
     *
     * @param int $nodeId
     * @return array
     */
    public function getStaticParamsMethod($nodeId){
        return $this->getNavigation()->getStaticParams($nodeId);
    }
    
    /**
     *
     * @param int $nodeId
     * @return array
     */
    public function getDynamicParamsMethod($nodeId) {
        return $this->getNavigation()->getDynamicParams($nodeId);
    }

    
    /**
     *
     * @param int $nodeId
     * @param string $key
     * @param mixed $value
     * @return bool 
     */
    public function setStaticParamMethod($nodeId,$key,$value) {
        return $this->getNavigation()->setStaticParam($nodeId, $key, $value);
    }

    
    /**
     *
     * @param int $nodeId
     * @param string $key
     * @param mixed $value
     * @return bool 
     */
    public function setDynamicParamMethod($nodeId,$key,$value) {
        return $this->getNavigation()->setDynamicParam($nodeId, $key, $value);
    }
    
    
    /**
     *
     * @param int $nodeId
     * @param int $beforeId 
     * @return bool
     */
    public function moveBeforeMethod($nodeId, $beforeId) {
        return $this->getNavigation()->moveBefore($nodeId, $beforeId);
    }
    
    /**
     *
     * @param int $nodeId
     * @param int $afterId 
     * @return bool
     */
    public function moveAfterMethod($nodeId, $afterId) {
        return $this->getNavigation()->moveAfter($nodeId, $afterId);
    }
    
    /**
     *
     * @param int $nodeId
     * @param int $intoId 
     * @return bool
     */
    public function moveIntoMethod($nodeId, $intoId) {
        return $this->getNavigation()->moveInto($nodeId, $intoId);
    }
    
    
    /**
     *
     * @param int $nodeId
     * @param string $title 
     * @return bool
     */
    public function setTitle($nodeId, $title) {
        return $this->getNavigation()->setTitle($nodeId, $title);
    }
    
    /**
     *
     * @param int $nodeId
     * @param string $url 
     * @return bool
     */
    public function setUrl($nodeId, $url) {
        return $this->getNavigation()->setUrl($nodeId, $url);
    }
    
    
    /**
     *
     * @param int $nodeId
     * @return array
     */
    public function getBranchMethod($nodeId) {
        return $this->getNavigation()->getBranch($nodeId);
    }
    
    /**
     *
     * @return array
     */
    public function getContentPluginsMethod() {
        $pd = $this->getPluginDescriptor();
        return $pd->getContentPlugins();
    }

    /**
     *
     * @return array
     */
    public function getExtensionPluginsMethod() {
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
    public function insertIntoMethod($intoId, $value, $pluginIdentifier) {
        $navigation = $this->getNavigation();
        $newId = $navigation->insertInto($intoId, array(
            'title' => $value['title'],
            'url' => isset($value['url']) ? $value['url'] : urlencode($value['title'])
                ));

        // Prepare page
        $this->_installContentPlugin($newId, $pluginIdentifier);

        return $newId;
    }

    public function deleteNodeMethod($nodeId) {
        // Find node
        // Get node branch
        $navigation = $this->getNavigation();
        $branch = $navigation->getBranch($nodeId, null, true);
        // Initiate delete hooks which will clean plugin associated resources
        $this->_deleteBranch($branch);

        // Delete node
        $navigation->delete($nodeId);

        // Return parent branch
        return $navigation->getBranch($branch['parentId']);
    }

    /**
     *  @param int $id
     * @param string|null $key
     *  @param mixed $content
     */
    public function setContentMethod($id, $key, $content) {
        $pluginIdentifier = $this->navigation->getStaticParam($id, self::PARAM_CONTENT_PLUGIN_IDENTIFIER);
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);

        $mvc = array(
            'module' => $plugin['module'],
            'controller' => $plugin['controller'],
            'action' => 'set',
            'key'=>$key,
            'content' => $content
        );
        return $this->_callMvc($id, $mvc, 'production');
    }

    /**
     * @param int $id
     * @param string|int $key
     * @param mixed $content
     */
    public function getContentMethod($id, $key) {
        $pluginIdentifier = $this->getnavigation()->getStaticParam($id, self::PARAM_CONTENT_PLUGIN_IDENTIFIER);
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);
        
        $mvc = array(
            'module' => $plugin['module'],
            'controller' => $plugin['controller'],
            'action' => 'get',
            'key'=>$key,
            'response'=>new stdClass()
        );

        $this->_callMvc($id, $mvc, 'production');
        $response = $mvc['response']->response;
        return $response;
    }

    /**
     * Set new home page
     * @param int $navigationId
     * @return array
     */
    public function setHomePageMethod($navigationId) {
        $navigation = $this->getNavigation();
        $oldHomePage = $this->getHomeNode();
        $oldHomePage['home']=0;
        $navigation->setHome($navigationId);
        $newHomePage = $this->getHomeNode();

        return array($oldHomePage, $newHomePage);
    }

    
    /**
     * Find home node and strip all irrelevant information from it
     * @return array
     */
    public function getHomeNode() {
        $node = $this->getNavigation()->findByHome();
        unset($node['tid']);
        unset($node['l']);
        unset($node['r']);
        unset($node['final']);
        unset($node['abstract']);
        unset($node['plugins']);
        return $node;
        
    }

    /**
     *
     * @param array $node 
     */
    protected function _deleteBranch($node) {
        $id = $node['id'];
        $pluginIdentifier = $this->getNavigation()->getStaticParam($id, self::PARAM_CONTENT_PLUGIN_IDENTIFIER);
        $p = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);


        $mvcParams = array(
            'module' => $p['module'],
            'controller' => $p['controller'],
            'action' => 'uninstallpage'
        );
        try {
            $this->_callMvc($id, $mvcParams, 'production');
        } catch (Exception $e) {
            
        }

        foreach ($node['childNodes'] as $cnode) {
            $this->_deleteBranch($cnode);
        }
    }

    protected function _getFrontController() {
        $application = $GLOBALS['application'];
        /* @var $application Zend_Application */
        $fc = $application->getBootstrap()->getPluginResource("frontcontroller")->getFrontController();

        // Initialize front controller
        $fc->throwExceptions(true);

        $response = new Zend_Controller_Response_Http();
        $fc->setResponse($response);

        $fc->setParam('bootstrap', $application->getBootstrap());
        // Add route 
        $r = $fc->getRouter();
        /* @var $r Zend_Controller_Router_Rewrite */
        $r->addRoute("default", new Azf_Controller_Router_Route_Fixed());
        return $fc;
    }

    /**
     * Initialize specific bootstrap env
     * @param type $env 
     */
    protected function _initBootstrapEnv($env) {
        $application = $GLOBALS['application'];
        /* @var $application Zend_Application */
        $application->getBootstrap()->envBootstrap($env);
    }

    /**
     *
     * @param int $id
     * @param array $mvcParams
     * @param null|string $inEnvironment
     * @return array 
     */
    protected function _callMvc($id, $mvcParams, $inEnvironment = null) {

        if ($inEnvironment) {
            $this->_initBootstrapEnv($inEnvironment);
        }
        // Get Front Controller
        $fc = $this->getFrontController();
        $response = new Zend_Controller_Response_Http();
        $route = $this->getRoute();

        $params = array(
            'id' => $id
                ) + $mvcParams;
        $route->setParams($params);
        // Dispatch request
        // Start caching
        ob_start();

        $fc->dispatch(null, $response);
        // End caching
        ob_get_clean();
        return $response->getBody(true);
    }
    
    
    /**
     *
     * @param int $nodeId
     * @param string $newType
     * @return boolean 
     */
    public function changePageTypeMethod($nodeId,$newType) {
        $pluginIdentifier = $this->getNavigation()->getStaticParam($nodeId, self::PARAM_CONTENT_PLUGIN_IDENTIFIER);
        if(!$pluginIdentifier){
            return false;
        }
        
        $this->_uninstallContentPlugin($nodeId);
        $this->_installContentPlugin($nodeId, $newType);
        return true;
    }

    protected function isAllowed($namespaces, $parameters) {
        // TODO fix me
        return true;
    }

    
    /**
     *
     * @param int $nodeId 
     */
    protected  function _uninstallContentPlugin($nodeId) {
        $staticParams = $this->getNavigation()->getStaticParams($nodeId);
        $mvcParams = array('action'=>'uninstallpage')+$staticParams;
        $this->_callMvc($nodeId, $mvcParams, 'production');
        $this->getNavigation()->deleteStaticParam($nodeId, "values");
    }

    
    /**
     *
     * @param int $nodeId
     * @param string $pluginIdentifier 
     */
    public function _installContentPlugin($nodeId,$pluginIdentifier) {
        $pluginDescriptor = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);
        $mvcParams = array('action'=>'installpage')+$pluginDescriptor;
        $this->_callMvc($nodeId, $mvcParams, 'production');
        $this->getNavigation()->setStaticParam($nodeId, self::PARAM_CONTENT_PLUGIN_IDENTIFIER, $pluginIdentifier);
    }

}
