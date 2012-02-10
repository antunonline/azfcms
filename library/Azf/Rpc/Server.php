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
     * @var array
     */
    protected $requestArgs = array();

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
     * @return array
     */
    public function getRequestArgs() {
        return $this->requestArgs;
    }

    /**
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function getRequestArg($name, $default = null) {
        if (!isset($this->requestArgs[$name])) {
            return $default;
        } else {
            return $this->requestArgs[$name];
        }
    }

    /**
     *
     * @param array $requestArgs 
     */
    public function setRequestArgs(array $requestArgs) {
        $this->requestArgs = $requestArgs;
    }

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
    
    /**
     *  @return string|boolean
     */
    public function getModuleName(){
        $moduleName = $this->getRequestArg("module","");
        if($moduleName && ctype_alpha($moduleName[0]) && ctype_alnum($moduleName)){
            return $moduleName;
        }
        else {
            return false;
        }
        
    }
    
    /**
     *  @return string|string
     */
    public function getProviderName(){
        $providerName = $this->getRequestArg("provider","");
        if($providerName && ctype_alpha($providerName[0]) && ctype_alnum($providerName)){
            return $providerName;
        }
        else {
            return false;
        }
    }

    function __construct() {
        
    }

    /**
     * This method will initialize all dependencies required by the server 
     */
    protected function _init() {
        $this->_initRequestArgs();
        // If module name or provider name is not valid or not provided
        if(!$this->getModuleName() || !$this->getProviderName()){
            return false;
        }
        $this->_initJsonServer();
        $this->_initModule();
        $this->_initClassName();
        $this->_initClassInstance();
        
        return true;
    }

    /**
     * Initialize JSON-RPC ZF server implementation 
     */
    protected function _initJsonServer() {
        $rpcServer = new Zend_Json_Server();
        $this->setRpcServer($rpcServer);
    }

    protected function _initRequestArgs() {
        if ($this->getRequestArgs() == false) {
            $this->setRequestArgs($_GET);
        }
    }
    
    protected function _initModule(){
        $moduleName = $this->getModuleName();
        $providerName = $this->getProviderName();
        $modulePath = APPLICATION_PATH."/modules/".strtolower($moduleName);
        $moduleBootstrapPath = $modulePath."/".ucfirst($modulePath)."Bootstrap";
        $bootstrapClassName = ucfirst($moduleName)."_".ucfirst($providerName);
        
        if(strtolower($moduleName)=="default"){
            return true;
        }
        
        if(!is_dir($modulePath)){
            return false;
        }
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace'=>strtolower($moduleName),
            'basePath'=>$modulePath
        ));
        
        if(is_file($moduleBootstrapPath) && is_readable($moduleBootstrapPath)){
           if(!include_once($moduleBootstrapPath)){
               return false;
           }
           
           $bootstrapInstance = new $bootstrapClassName();
           /* @var $bootstrapInstance Zend_Application_Module_Bootstrap */
           $bootstrapInstance->bootstrap("rpc");
        }
        
        return true;
    }

    /**
     * Construct class name from given parameters 
     */
    protected function _initClassName() {
        $module = $this->getModuleName();
        $provider = $this->getProviderName();

        // If module and provider values are valid (does not equals to false)
        if ($module && $provider) {
            if (strtolower($module) == "default") {
                $module = "application";
            }

            $providerClassName = ucfirst($module) . "_Rpc_" . ucfirst($provider);
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
        if(!$this->_init()){
            echo "null";
        }

        $server = $this->getRpcServer();
        $providerInstance = $this->getProviderInstance();

        if (!$providerInstance) {
            echo "false";
            return false;
        } else {
            $server->setClass($providerInstance);
            $module = $this->getModuleName();
            $provider = $this->getProviderName();
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

