<?php

class Application_Resolver_Navigation extends Azf_Service_Lang_Resolver {

    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigation;

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

    public function initialize() {
        parent::initialize();
        $this->setNavigation(Zend_Registry::get("navigationModel"));
    }

    protected function _execute(array $namespaces, array $parameters) {
        if (sizeof($namespaces) != 1) {
            return null;
        }

        $method = array_shift($namespaces);
        
        
        if(in_array($method,array('insertInto'))){
            call_user_method_array($method, $this, $parameters);
        }
        else if (method_exists($this->getNavigation(), $method)) {
            return call_user_method_array($method, $this->getNavigation(), $parameters);
        } else {
            return null;
        }
    }

    /**
     *
     * @return array
     */
    public function getContentPlugins() {
        $pd = new Azf_Plugin_Descriptor();
        return $pd->getContentPlugins();
    }

    /**
     *
     * @return array
     */
    public function getExtensionPlugins() {
        $pd = new Azf_Plugin_Descriptor();
        return $pd->getExtensionPlugins();
    }
    
    public function insertInto($id, $value, $pluginIdentifier){
        $navigatino = $this->getNavigation();
        $id = $navigatino->insertInto($id, array(
            'title'=>$value['title'],
            'url'=>isset($value['url'])?$value['url']:""
        ));
        $navigatino->setStaticParam($id, 'pluginIdentifier', $pluginIdentifier);
        return $id;
    }
    
    protected function isAllowed($namespaces, $parameters) {
        // TODO fix me
        return true;
    }

}
