<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Doh extends Azf_Service_Lang_Resolver {

    const PARAM_CONTENT_PLUGIN_IDENTIFIER = "pluginIdentifier";
    const EXTENSION_PLUGIN_DEVELOPMENT_TITLE = 'Extension Plugin Development';
    const CONTENT_PLUGIN_DEVELOPMENT_TITLE = 'Extension Plugin Development';

    /**
     * 
     * @return Azf_Model_Tree_Navigation
     */
    public function getNavigation() {
        return Zend_Registry::get("navigationModel");
    }

    /**
     * 
     * @return \Azf_Model_DbTable_Plugin
     */
    public function getPluginModel() {
        return new Azf_Model_DbTable_Plugin();
    }

    /**
     * 
     * @return \Azf_Model_DbTable_NavigationPlugin
     */
    public function getNavigationPluginModel() {
        return new Azf_Model_DbTable_NavigationPlugin();
    }

    /**
     * @return Azf_Plugin_Extension_Manager
     */
    public function getPluginManager() {
        return new Azf_Plugin_Extension_Manager();
    }
    
    
    /**
     * 
     * @return \Azf_Plugin_Descriptor
     */
    public function getPluginDescriptor(){
        return new Azf_Plugin_Descriptor();
    }

    protected function isAllowed($namespaces, $parameters) {
        return true;
    }

    public function uploadFilesMethod() {
        return $_FILES;
    }

    public function getQueryLangStoreQueryRangeMethod($query = null, $options = array(), $staticOptions = array()) {
        $dojoHelper = $this->getHelper("dojo");
        /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */

        $start = $dojoHelper->getQueryOptionsStart($options);
        $count = $dojoHelper->getQueryOptionsCount($options);

        $result = array();
        for ($i = 0; $i < $count; $i++) {
            $result[] = array(
                'id' => $start + $i,
                'name' => "Name " . ($start + $i)
            );
        }

        return $dojoHelper->constructQueryResult($result, pow(10, 8));
    }

    /**
     * 
     * @return array
     */
    public function getNavigationRootBranch() {
        $rootNode = $this->getNavigation()->getRootNode();
        return $this->getNavigation()->getBranch($rootNode['id']);
    }

    /**
     * 
     * @param int $id
     * @return array
     */
    public function getNavigationBranchById($id) {
        return $this->getNavigation()->getBranch($id);
    }

    /**
     * 
     * @param string $title
     * @return array|null
     */
    public function fetchNavigationBranchByTitle($title) {
        $iterate = function($title, $branch,$iterate) {
                    if ($branch['title'] == $title) {
                        return $branch;
                    } else {
                        foreach ($branch['childNodes'] as $node) {
                            if (null !== $foundBranch = $iterate($title, $node,$iterate)) {
                                return $foundBranch;
                            }
                        }
                    }
                };

        $rootBranch = $this->getNavigationRootBranch();
        return $iterate($title, $rootBranch,$iterate);
    }

    /**
     * 
     * @return id
     */
    public function createExtensionPluginDevelopmentBranch() {
        $rootNode = $this->getNavigationRootBranch();
        $extensionPluginDevelopmentNode = array(
            'title' => self::EXTENSION_PLUGIN_DEVELOPMENT_TITLE,
            'url' => self::EXTENSION_PLUGIN_DEVELOPMENT_TITLE,
            'disabled' => false
        );

        return $this->getNavigation()->insertInto($rootNode['id'], $extensionPluginDevelopmentNode);
    }
    
    
    /**
     * 
     * @param int $intoId
     * @param string $type
     */
    public function createExtensionPluginBranch($intoId, $type) {
        return $this->getNavigation()->insertInto($intoId, array(
            'title'=>$type,
            'url'=>$type,
            'disabled'=>false
        ));
    }

    public function registerExtensionPlugin($type) {
        $plugin = array(
            'name' => $type,
            'description' => $type,
            'type' => $type,
            'region' => $type,
            'weight' => 0,
        );
        return $this->getPluginModel()->insertPlugin($plugin);
    }
    
    
    /**
     * 
     * @param string $type
     * @return array|null
     */
    public function fetchExtensionPluginByRegion($type) {
        $row =  $this->getPluginModel()->fetchRow(array('region=?'=>$type,'type=?'=>$type));
        if($row){
            return $row->toArray();
        } else {
            return null;
        }
    }
    
    
    /**
     * 
     * @param int $navigationId
     * @param int $pluginId
     * @return int|null
     */
    public function fetchNavigationPluginBinding($navigationId, $pluginId) {
        $where = array(
            'navigationId=?'=>$navigationId,
            'pluginId=?'=>$pluginId
        );
        $row  = $this->getNavigationPluginModel()->fetchRow($where);
        
        
        if($row){
            return $row->id;
        } else {
            return null;
        }
    }
    
    
    
    /**
     * 
     * @param int $navigationId
     * @param int $pluginId
     */
    public function bindNavigationPlugin($navigationId, $pluginId) {
        $this->getNavigationPluginModel()->bind($navigationId, $pluginId, 0);
    }

    
    /**
     * 
     * @param string $type
     * @return array array('pluginId'=>(int),'navigationId'=>(int))
     */
    public function initializeExtensionPluginMethod($type) {
        $developmentBranch = $this->fetchNavigationBranchByTitle(self::EXTENSION_PLUGIN_DEVELOPMENT_TITLE);
        if (null == $developmentBranch) {
            $this->createExtensionPluginDevelopmentBranch();
            $developmentBranch = $this->fetchNavigationBranchByTitle(self::EXTENSION_PLUGIN_DEVELOPMENT_TITLE);
        }

        $pluginBranch = $this->fetchNavigationBranchByTitle($type);
        if (null === $pluginBranch) {
            $id = $this->createExtensionPluginBranch($developmentBranch['id'],$type);
            $pluginBranch = $this->fetchNavigationBranchByTitle($type);
        }
        
        $extensionPlugin = $this->fetchExtensionPluginByRegion($type);
        if(!$extensionPlugin){
            $this->registerExtensionPlugin($type);
            $extensionPlugin = $this->fetchExtensionPluginByRegion($type);
            $this->getPluginManager()->setUp($type, $extensionPlugin['id']);
        }
        
        $navigationPluginBinding = $this->fetchNavigationPluginBinding($pluginBranch['id'],$extensionPlugin['id']);
        if(!$navigationPluginBinding){
            $this->bindNavigationPlugin($pluginBranch['id'],$extensionPlugin['id']);
        }
        
        return array(
            'pluginId'=>$extensionPlugin['id'],
            'navigationId'=>$pluginBranch['id']
        );
    }

    
    /**
     * 
     * @param string $type
     * @return array array('pluginId'=>(int),'navigationId'=>(int))
     */
    public function reinitializeExtensionPluginMethod($type) {
       $params = $this->initializeExtensionPluginMethod($type);
       $pluginId = $params['pluginId'];
       
       $manager = $this->getPluginManager();
       $manager->tearDown($type, $pluginId);
       $manager->setUp($type, $pluginId);
        
       return $params;   
    }
    
    
    
    public function createContentPluginDevelopmentBranch() {
        $root= $this->getNavigation()->getRootNode();
        $rootid = $root['id'];
        
        $this->getNavigation()->insertInto($rootid, array(
            'title'=>self::CONTENT_PLUGIN_DEVELOPMENT_TITLE,
            'url'=>self::CONTENT_PLUGIN_DEVELOPMENT_TITLE,
            'disabled'=>false
        ));
    }
    
    public function createContentPluginBranch($intoId, $identifier) {
        $node = array(
            'url'=>$identifier,
            'title'=>$identifier,
            'disabled'=>false
        );
        
        $this->getNavigation()->insertInto($intoId, $node);
    }
    
    /**
     * 
     * @param string $identifier
     * @return int
     */
    public function initializeContentPluginMethod($identifier){
        $developmentBranch = $this->fetchNavigationBranchByTitle(self::CONTENT_PLUGIN_DEVELOPMENT_TITLE);
        if(!$developmentBranch){
            $this->createContentPluginDevelopmentBranch();
            $developmentBranch = $this->fetchNavigationBranchByTitle(self::CONTENT_PLUGIN_DEVELOPMENT_TITLE);
        }
        
        $pluginBranch = $this->fetchNavigationBranchByTitle($identifier);
        if(!$pluginBranch){
            $this->createContentPluginBranch($developmentBranch['id'],$identifier);
            $pluginBranch = $this->fetchNavigationBranchByTitle($identifier);
            
            $pluginParams = $this->getPluginDescriptor()->getContentPlugin($identifier);
        $pluginParams['action'] = "installpage";
        
        $frontController = $this->getHelper("frontController");
        /* @var $frontController Azf_Service_Lang_ResolverHelper_FrontController */
        $frontController->callMvc($pluginBranch['id'],$pluginParams,'production');
        $this->getNavigation()->setStaticParam($pluginBranch['id'], self::PARAM_CONTENT_PLUGIN_IDENTIFIER, $pluginIdentifier);
        }
        
        
        
        
        
        return $pluginBranch['id'];
    }
    
    public function reinitializeContentPluginMethod($identifier) {
        $id = $this->initializeContentPluginMethod($identifier);
        
        $pluginParams = $this->getPluginDescriptor()->getContentPlugin($identifier);
        $pluginParams['action'] = "installpage";
        
        $frontController = $this->getHelper("frontController");
        /* @var $frontController Azf_Service_Lang_ResolverHelper_FrontController */
        $frontController->callMvc($id,$pluginParams,'production');
        $this->getNavigation()->setStaticParam($id, self::PARAM_CONTENT_PLUGIN_IDENTIFIER, $pluginIdentifier);

        
        return $id;
    }

}
