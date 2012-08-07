<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DojoUtil
 *
 * @author antun
 */
class Azf_Service_Lang_ResolverHelper_FrontController {

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

    
    /**
     * 
     * @return Zend_Controller_Front
     */
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
    public function callMvc($id, $mvcParams, $inEnvironment = null) {

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

}
