<?php

/**
 * 
 *
 * @author antun
 */
class Azf_Plugin_Descriptor {

    /**
     *
     * @var array
     */
    protected $_contentPlugins = null;

    /**
     *
     * @var array
     */
    protected $_extensionPlugins = null;

    public function getContentPlugins() {
        if ($this->_contentPlugins === null) {
            $this->setContentPlugins($this->_loadContentPlugins());
        }
        return $this->_contentPlugins;
    }

    public function setContentPlugins($contentPlugins) {
        $this->_contentPlugins = $contentPlugins;
    }

    /**
     *
     * @param array $plugin 
     */
    public function addContentPlugin(array $plugin) {
        $this->_contentPlugins[] = $plugin;
    }

    public function getExtensionPlugins() {
        if ($this->_extensionPlugins === null) {
            $this->setExtensionPlugins($this->_loadExtensionPlugins());
        }
        return $this->_extensionPlugins;
    }

    /**
     * 
     */
    public function addExtensionPlugin(array $plugin) {
        $this->_extensionPlugins[] = $plugin;
    }

    public function setExtensionPlugins($extensionPlugins) {
        $this->_extensionPlugins = $extensionPlugins;
    }
    
    
    /**
     *
     * @param string $pluginIdentifier 
     * @return array|null
     */
    public function getContentPlugin($pluginIdentifier){
        $plugins = $this->getContentPlugins();
        return $this->_findPlugin($pluginIdentifier, $plugins);
    }
    
    
    /**
     *
     * @param string $pluginIdentifier 
     * @return array|null
     */
    public function getExtensionPlugin($pluginIdentifier){
        $plugins = $this->getExtensionPlugins();
        return $this->_findPlugin($pluginIdentifier, $plugins);
    }
    
    /**
     *
     * @param type $identifier
     * @param array $plugins 
     */
    protected function _findPlugin($identifier,array $plugins){
        foreach($plugins as $plug){
            if($plug['pluginIdentifier']==$identifier){
                return $plug;
            }
        } 
        return null;
    }

    public function __construct() {
        ;
    }

    /**
     * @return DirectoryIterator 
     */
    protected function _getContentPluginDirectoryIterator() {
        return new DirectoryIterator(APPLICATION_PATH . "/configs/descriptor/plugin/content");
    }

    /**
     * @return DirectoryIterator 
     */
    protected function _getExtensionPluginDirectoryIterator() {
        return new DirectoryIterator(APPLICATION_PATH . "/configs/descriptor/plugin/extension");
    }

    /**
     *
     * @param DirectoryIterator $pdi
     * @param Azf_Plugin_Descriptor_Parser_Abstract $pp 
     * @return array
     */
    protected function _loadPlugins(DirectoryIterator $pdi, Azf_Plugin_Descriptor_Parser_Abstract $pp) {

        $plugins = array();
        foreach ($pdi as $file) {
            /* @var $file DirectoryIterator */
            if ($file->getFileInfo()->getExtension() == "xml") {
                // Parse file
                try {
                    $plugins[] = $pp->parseFile($file->getRealPath());
                } catch (Azf_Plugin_Descriptor_Parser_Exception $e) {
                    
                }
            }
        }

        return $plugins;
    }

    /**
     *
     * @return array
     */
    protected function _loadExtensionPlugins() {
        return $this->_loadPlugins($this->_getExtensionPluginDirectoryIterator(), new Azf_Plugin_Descriptor_Parser_Extension());
    }

    /**
     *
     * @return array
     */
    protected function _loadContentPlugins() {
        return $this->_loadPlugins($this->_getContentPluginDirectoryIterator(), new Azf_Plugin_Descriptor_Parser_Content());
    }

}

