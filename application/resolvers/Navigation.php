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

        $method = array_pop($namespaces);

        if (method_exists($this->getNavigation(), $method)) {
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

}
