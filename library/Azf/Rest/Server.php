<?php

/**
 * Description of Server
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Rest_Server {

    /**
     *
     * @var Azf_Rest_Request
     */
    protected $request = null;

    /**
     *
     * @var Azf_Rest_Response
     */
    protected $response = null;

    /**
     *
     * @var Azf_Rest_Provider_Abstract
     */
    protected $provider = null;

    /**
     *
     * @return Azf_Rest_Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     *
     * @param Azf_Rest_Request $request 
     */
    public function setRequest(Azf_Rest_Request $request) {
        $this->request = $request;
    }

    /**
     *
     * @return Azf_Rest_Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     *
     * @param Azf_Rest_Response $response 
     */
    public function setResponse(Azf_Rest_Response $response) {
        $this->response = $response;
    }

    /**
     *
     * @return Azf_Rest_Provider_Abstract
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     *
     * @param Azf_Rest_Provider_Abstract $provider 
     */
    public function setProvider(Azf_Rest_Provider_Abstract $provider) {
        $this->provider = $provider;
    }

    public function __construct() {
        ;
    }

    /**
     *  
     */
    protected function _init() {
        $this->_initRequest();
        $this->_initResponse();
        $this->_initModule();
        $this->_initProvider();
    }

    /**
     * Initialize request object
     */
    protected function _initRequest() {
        $this->setRequest(new Azf_Rest_Request());
        // If request is not properly initialized
        if ($this->getRequest()->isValid() == false) {
            throw new RuntimeException("Request object could not be properly initialized");
        }
    }

    protected function _initResponse() {
        $this->setResponse(new Azf_Rest_Response());
    }

    protected function _initModule() {
        if ($this->getRequest()->getModuleName() == "application") {
            return;
        }
        $module = $this->getRequest()->getModuleName();
        $modulePath = APPLICATION_PATH . "/modules/" . $module;
        $bootstrapPath = $modulePath . "/Bootstrap.php";
        $bootstrapClassName = ucfirst($module) . "_Bootstrap";

        if (!is_dir($modulePath)) {
            throw new RuntimeException("Module directory for module \"$module\" does not exist");
        }

        $resourceLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => ucfirst($module),
                    'basePath' => $modulePath
                ));

        if (is_readable($bootstrapPath)) {
            include_once($bootstrapPath);
            if (class_exists($bootstrapClassName)) {
                $bootstrapInstance = new $bootstrapClassName(Zend_Registry::get("application"));
                /* @var $bootstrapInstance Zend_Application_Module_Bootstrap */
                $bootstrapInstance->setResourceLoader($resourceLoader);
                $bootstrapInstance->bootstrap();
            }
        }
    }

    protected function _initProvider() {
        $request = $this->getRequest();
        $providerClassName = ucfirst($request->getModuleName()) . "_Rest_" . ucfirst($request->getProviderName());

        if (!Zend_Loader_Autoloader::getInstance()->autoload($providerClassName)) {
            throw new Zend_Loader_Exception("Could not load \"$providerClassName\" class");
        }

        $provider = new $providerClassName();
        if ($provider instanceof Azf_Rest_Provider_Abstract == false) {
            throw new RuntimeException("Initialized provider instance is not a subclass of Azf_Rest_Provider_Abstract");
        }

        $provider->setRequest($this->getRequest());
        $provider->init();
        $this->setProvider($provider);
    }

    protected function _handle() {
        $provider = $this->getProvider();
        $request = $this->getRequest();
        $response = $this->getResponse();

        if (!$this->_isAllowed($provider)) {
            $response->setBody("Not allowed");
            $response->setResponseCode(Azf_Rest_Response::HTTP_UNAUTHORIZED);
            return;
        }

        switch ($this->getRequest()->getMethod()) {
            case Azf_Rest_Request::METHOD_GET:
                if ($request->getId()) {
                    $responseBody = $provider->get($request, $response);
                } else {
                    $responseBody = $provider->index($request, $response);
                }
                if ($response->getResponseCode() == false) {
                    $response->setResponseCode(Azf_Rest_Response::HTTP_OK);
                }
                break;
            case Azf_Rest_Request::METHOD_POST:
                $responseBody = $provider->post($request, $response);

                if ($response->getResponseCode() == false) {
                    $response->setResponseCode(Azf_Rest_Response::HTTP_CREATED);
                }
                break;
            case Azf_Rest_Request::METHOD_PUT:
                $responseBody = $provider->put($request, $response);
                if ($response->getResponseCode() == false) {
                    $response->setResponseCode(Azf_Rest_Response::HTTP_OK);
                }
                break;
            case Azf_Rest_Request::METHOD_DELETE:
                $responseBody = $provider->delete($request, $response);
                if ($response->getResponseCode() == false) {
                    $response->setResponseCode(Azf_Rest_Response::HTTP_OK);
                }
                break;
        }

        $response->addBody(json_encode($responseBody));
        $response->doResponse();
    }

    protected function _isAllowed(Azf_Rest_Provider_Abstract $provider) {
        return $provider->isAllowed($this->getRequest(), $this->getRequest()->getMethod(), $this->getRequest()->getId());
    }

    public function handle() {
        try {
            $this->_init();
            $this->_handle();
        } catch (Exception $e) {
            echo $e->getMessage();
            if (Zend_Registry::isRegistered("logLevel") ? Zend_Registry::get("logLevel") : 0 & E_ALL == E_ALL) {
                Zend_Registry::get("log")->log($e->getMessage(), Zend_Log::DEBUG);
            }
            return;
        }
    }

}

