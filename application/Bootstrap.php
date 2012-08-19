<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    
    protected $_initEnvs = array();
    
    /**
     *
     * @var Bootstrap
     */
    protected static $instance;
    
    /**
     *
     * @return Bootstrap
     */
    public static function getInstance(){
        return self::$instance;
    }
    
    /**
     *
     * @param string $env 
     */
    public function setEnvironment($env){
        $this->_environment = $env;
    }
    
    /**
     * Store self-instance 
     */
    public function _initInstance(){
        self::$instance = $this;
    }
    
    /**
     * Will return true if we are bootstraping MVC environment
     * 
     * @return boolean
     */
    public function isMvcEnvironment(){
        return in_array($this->getEnvironment(),array('production','testing','development'));
    }
    
    
    /**
     * Will return true if we are bootstraping RPC environment
     * @return boolean 
     */
    public function isRpcEnv(){
        if (0 === strpos($this->getEnvironment(), "json-rpc")) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Will return true if we are bootstraping REST env
     * @return boolean 
     */
    public function isRestEnv(){
        if (0 !== strpos($this->getEnvironment(), "json-rest")) {
            return false;
        } else {
            return true;
        }
    }
    
    
    /**
     * Will return true if we are bootstraping LANG env
     * @return boolean 
     */
    public function isLangEnv(){
        if (0 !== strpos($this->getEnvironment(), "json-lang")) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Bootstrap specific environment
     * @param string $env
     * @return null
     */
    public function envBootstrap($env){
        if(in_array($env,$this->_initEnvs)){
            return ;
        }
        // Set env. name
        $this->setEnvironment($env);
        // Get local bootstrap method names
        $localResourceNames = $this->getClassResourceNames();
        // Remove local resource names from run list
        $this->_run = array_diff($localResourceNames,$this->_run);
        // Run
        $this->bootstrap();
    }
    
    
    
    public function _initSaveEnv(){
        // Registered this env as bootstraped
        if(!in_array($this->getEnvironment(),$this->_initEnvs)){
            $this->_initEnvs[] = $this->getEnvironment();
        }
        
    }
    
    public function _initLayout(){
        if($this->isMvcEnvironment()==false)
            return;
        
        static $isInit;
        if(isset($isInit)){
            return;
        } else {
            $isInit = true;
        }
        
        Zend_Layout::startMvc(array(
            'layoutPath'=>APPLICATION_PATH."/views/layouts",
            'layout'=>$this->getOption("defaultTemplate")
        ));
    }

    /**
     * Initialize Azf classpath 
     */
    public function _initAzfClassPath() {
        static $isInit;
        if(isset($isInit)){
            return;
        } else {
            $isInit = true;
        }
        
        Zend_Loader_Autoloader::getInstance()->registerNamespace("Azf");
        
    }
    
    

    /**
     *Initialize RPC module 
     */
    public function _initRpcModule() {
        if(!$this->isRpcEnv()){
            return;
        }
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        
        $module = (string) $_GET['module'];
        if(!ctype_alnum($module)){
            throw new HttpRequestException("Invalid module name specified");
        }
        $moduleDir = APPLICATION_PATH . '/modules/' . $module;
        if ($module && ctype_alpha($module[0]) && ctype_alnum($module) && is_dir($moduleDir)) {
            $resource = new Zend_Application_Resource_Modules(array(
                        $module
                    ));
            $resource->setBootstrap($this);
            $resource->init();
        }
    }
    
    public function _initGlobalLoader(){
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        
        $this->getResourceLoader()
                ->addResourceType("plugins", "plugins","Plugin");
        $this->getResourceLoader()
                ->addResourceType("filters", "filters","Filter");
    }

    /**
     * Initialize rpc namespace 
     */
    public function _initRpcLoader() {
        if(!$this->isRpcEnv()){
            return;
        }
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        
        $this->getResourceLoader()
                ->addResourceType("rpcs", "rpcs", "Rpc");
    }


    
    public function _initRestLoader() {
        if(!$this->isRestEnv()){
            return false;
        } 
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $this->getResourceLoader()
                ->addResourceType("rests", "rests", "Rest");
    }

    
    public function _initLangLoader() {
        if(!$this->isLangEnv()){
            return false;
        }
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $this->getResourceLoader()
                ->addResourceType("resolvers", "resolvers", "Resolver");
    }
    
    /**
     * Initialize log obj.      */
    public function _initLog() {
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $logWriter = new Zend_Log_Writer_Null();
        $log = new Zend_Log($logWriter);
        Zend_Registry::set("log",$log);
        Zend_Registry::set("logLevel",  Zend_Registry::isRegistered("logLevel")?Zend_Registry::get("logLevel"):E_ERROR);
    }
    
    /**
     * Initialize navigation model
     * @return \Azf_Model_Tree_Navigation 
     */
    public function _initNavigationModel(){
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $dbAdapter = $this->getPluginResource("db")->getDbAdapter();
        
        $navigation = new Azf_Model_Tree_Navigation($dbAdapter);
        Zend_Registry::set("navigationModel", $navigation);
        
        return $navigation;
    }
    
    /**
     * Initialize MVC context action helper
     * @return type 
     */
    public function _initContextActionHelper(){
        if(!$this->isMvcEnvironment())
            return ;
        
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        Zend_Controller_Action_HelperBroker::addPrefix("Azf_Controller_Action_Helper");
    }
    
    
    
    /**
     * Initialize default route
     */
    public function _initRoute(){
        if(!$this->isMvcEnvironment())
            return;
        
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $navigation = $this->getResource("navigationModel");
        $route = new Azf_Controller_Router_Route_Default($navigation);
        $this->getPluginResource("frontcontroller")->getFrontController()->getRouter()->addRoute('default',$route);
    }
    
    public function _initBootstrapPlugin(){
        if(!$this->isMvcEnvironment())
            return;
        
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        Zend_Controller_Front::getInstance()->registerPlugin(new Azf_Controller_Plugin_Bootstrap());
    }
    
    /**
     * INitialize session env 
     */
    public function _initSession(){
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        Zend_Session::start();
    }
    
    
    
    public function _initDefaulTemplateIdentifier(){
        static $isInit;if(isset($isInit)){return;} else {$isInit = true;}
        $defaultTemplate = $this->getOption("defaultTemplate");
        Zend_Registry::set("defaultTemplate",$defaultTemplate);
    }
}

