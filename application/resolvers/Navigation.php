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
        $overrideMethod = 'override' . ucfirst($method);


        /**
         * If override method exists 
         */
        if (method_exists($this, $overrideMethod)) {
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
    public function overrideInsertInto($intoId, $value, $pluginIdentifier) {
        $navigation = $this->getNavigation();
        $newId = $navigation->insertInto($intoId, array(
            'title' => $value['title'],
            'url' => isset($value['url']) ? $value['url'] : urlencode($value['title'])
                ));

        // Prepare page
        $this->_prepareInseredPage($newId, $pluginIdentifier);

        $navigation->setStaticParam($newId, 'pluginIdentifier', $pluginIdentifier);
        return $newId;
    }

    /**
     * This method will prepare newly insered by 
     */
    public function _prepareInseredPage($id, $pluginIdentifier) {
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);

        // Call MVC
        $this->_callMvc($id, array('action' => 'installpage') + $plugin, "production");
    }

    public function overrideDeleteNode($nodeId) {
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
     *  @param mixed $content
     */
    public function overrideSetContent($id, $content) {
        $pluginIdentifier = $this->navigation->getStaticParam($id, "pluginIdentifier");
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);

        $mvc = array(
            'module' => $plugin['module'],
            'controller' => $plugin['controller'],
            'action' => 'set',
            'content' => $content
        );
        return $this->_callMvc($id, $mvc, 'production');
    }

    /**
     * @param int $id
     * @param mixed $content
     */
    public function overrideGetContent($id) {
        $pluginIdentifier = $this->navigation->getStaticParam($id, "pluginIdentifier");
        $plugin = $this->getPluginDescriptor()->getContentPlugin($pluginIdentifier);

        $mvc = array(
            'module' => $plugin['module'],
            'controller' => $plugin['controller'],
            'action' => 'get',
        );

        return $this->_callMvc($id, $mvc, 'production');
    }

    /**
     * Set new home page
     * @param int $navigationId
     * @return array
     */
    public function overrideSetHomePage($navigationId) {
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
        $pluginIdentifier = $this->getNavigation()->getStaticParam($id, 'pluginIdentifier');
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

    protected function isAllowed($namespaces, $parameters) {
        // TODO fix me
        return true;
    }

}
