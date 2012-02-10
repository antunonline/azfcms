<?php

/**
 * Description of Server
 *  // TODO 
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Rpc_Server {

    /**
     *
     * @var Zend_Json_Server
     */
    protected $rpcServer;

    /**
     * @var string
     */
    protected $providerClassName;

    /**
     *
     * @var Azf_Rpc_Provider_Abstract
     */
    protected $providerInstance;

    /**
     *
     * @return Zend_Json_Server
     */
    public function getRpcServer() {
        return $this->rpcServer;
    }

    /**
     *
     * @param Zend_Json_Server $rpcServer 
     */
    public function setRpcServer(Zend_Json_Server $rpcServer) {
        $this->rpcServer = $rpcServer;
    }

    /**
     *
     * @return string
     */
    public function getProviderClassName() {
        return $this->providerClassName;
    }

    /**
     *
     * @param string $providerClassName 
     */
    public function setProviderClassName($providerClassName) {
        $this->providerClassName = $providerClassName;
    }

    /**
     *
     * @return Azf_Rpc_Provider_Abstract
     */
    public function getProviderInstance() {
        return $this->providerInstance;
    }

    /**
     *
     * @param Azf_Rpc_Provider_Abstract $providerInstance 
     */
    public function setProviderInstance(Azf_Rpc_Provider_Abstract $providerInstance) {
        $this->providerInstance = $providerInstance;
    }

    function __construct() {
        $this->_init();
    }

    /**
     * This method will initialize all dependencies required by the server 
     */
    protected function _init() {
        $this->_initJsonServer();
        $this->_initClassName();
        $this->_initClassInstance();
    }

    /**
     * Initialize JSON-RPC ZF server implementation 
     */
    protected function _initJsonServer() {
        $rpcServer = new Zend_Json_Server();
        $this->setRpcServer($rpcServer);
    }

    /**
     * Construct class name from given parameters 
     */
    protected function _initClassName() {
        $module = $_GET['module'];
        $provider = $_GET['provider'];

        if ($module && ctype_alpha($module[0]) && ctype_alnum($module)) {
            $cleanModule = $module;
        } else {
            $module = "";
        }

        if ($provider && ctype_alpha($provider[0]) && ctype_alnum($provider)) {
            $cleanProvider = $provider;
        } else {
            $module = "";
        }

        if ($cleanModule && $cleanProvider) {
            if (strtolower($cleanModule) == "default") {
                $cleanModule = "application";
            }

            $providerClassName = ucfirst($cleanModule) . "_Rpc_" . ucfirst($provider);
        } else {
            $providerClassName = "";
        }

        $this->setProviderClassName($providerClassName);
    }

    /**
     * Initialize provider instance, if the request is properly formatted 
     */
    protected function _initClassInstance() {

        $className = $this->getProviderClassName();
        if ($className && Zend_Loader_Autoloader::getInstance()->autoload($className)) {
            $instance = new $className();
        } else {
            $instance = null;
        }

        $this->setProviderInstance($instance);
    }

    /**
     * Is the user allowed to invoke the method.
     * @return boolean 
     */
    protected function _isAllowed() {
        $provider = $this->getProviderInstance();
        $provider->initAcl();

        if ($provider->isAllowed($this->getRpcServer()->getRequest()->getMethod())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Initialize RPC provider 
     */
    protected function _initProvider() {
        $provider = $this->getProviderInstance();
        $provider->init();
    }

    /**
     * Handle the result.
     * @return boolean 
     */
    public function handle() {
        $server = $this->getRpcServer();
        $providerInstance = $this->getProviderInstance();

        if (!$providerInstance) {
            echo "false";
            return false;
        } else {
            $server->setClass($providerInstance);
            $module = $_GET['module'];
            $provider = $_GET['provider'];
        }

        // If it is a GET request, return SMD
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            $server->setTarget("/json-rpc.php?module=$module&provider=$provider")
                    ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
            $smd = $server->getServiceMap();

            // Set Dojo compatibility:
            $smd->setDojoCompatible(true);

            header('Content-Type: application/json');
            echo $smd;
        }
        // If it is a POST request, try to invoke the service
        else {
            if ($this->_isAllowed()) {
                $this->_initProvider();
                echo $server->handle();
            } else {
                return false;
            }
        }
    }

}

