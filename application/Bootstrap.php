<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    
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
     * Initialize Azf classpath 
     */
    public function _initAzfClassPath() {
        Zend_Loader_Autoloader::getInstance()->registerNamespace("Azf");
    }
    

    /**
     *Initialize RPC module 
     */
    public function _initRpcModule() {
        if(!$this->isRpcEnv()){
            return;
        }
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

    /**
     * Initialize rpc namespace 
     */
    public function _initRpcLoader() {
        if(!$this->isRpcEnv()){
            return;
        }
        $this->getResourceLoader()
                ->addResourceType("rpcs", "rpcs", "Rpc");
    }


    
    public function _initRestLoader() {
        if(!$this->isRestEnv()){
            return false;
        } 
        $this->getResourceLoader()
                ->addResourceType("rests", "rests", "Rest");
    }

    
    public function _initLangLoader() {
        if(!$this->isLangEnv()){
            return false;
        }
        $this->getResourceLoader()
                ->addResourceType("resolvers", "resolvers", "Resolver");
    }
    
    /**
     * Initialize log obj. 
     */
    public function _initLog() {
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
        
        Zend_Controller_Action_HelperBroker::addPrefix("Azf_Controller_Action_Helper");
    }
    
    
    
    /**
     * Initialize default route
     */
    public function _initRoute(){
        if(!$this->isMvcEnvironment())
            return;
        
        $navigation = $this->getResource("navigationModel");
        $route = new Azf_Controller_Router_Route_Default($navigation);
        $this->getPluginResource("frontcontroller")->getFrontController()->getRouter()->addRoute('default',$route);
    }
    
    /**
     * INitialize session env 
     */
    public function _initSession(){
        Zend_Session::start();
    }
    
    
    /**
     * 
     */
    public function _initAuth(){
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $dbAdapter = $this->getPluginResource("db")->getDbAdapter();
            
            $userModel = new Azf_Model_User(array('db'=>$dbAdapter));
            $authAdapter = new Azf_Auth_Adapter_User($userModel);
            $authAdapter->setLoginName("Guest");
            $authAdapter->setPassword("");
            
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if($result->isValid()==false){
                throw new RuntimeException("Guest user could not be logged in");
            }
            $identity = $auth->getIdentity();
        } else {
            $identity = Zend_Auth::getInstance()->getIdentity();
        }
        
        return $identity;
    }
    
    /**
     * Initialize ACL
     */
    public function _initAcl(){
        $this->bootstrap("session");
        if(Zend_Session::namespaceIsset("acl")){
            $acl = Zend_Session::namespaceGet("acl");
        } else {
            $acl = new Zend_Acl();
            $uid = $this->bootstrap("auth")->id;
            $role = "user";
            $dbAdapter= $this->getPluginResource("db")->getDbAdapter();
            $userModel = new Azf_Model_User(array("db"=>$dbAdapter));
            
            $aclRules = $userModel->getUserAclRules($uid);
            foreach($aclRules as $rule){
                $acl->allow($role, $rule['resource'], $rule['privilege']);
            }
            
            $namespace = new Zend_Session_Namespace("acl");
            $namespace->acl = $acl;
        }
        
        Zend_Registry::set("acl",$acl);
        return $acl;
    }
}

