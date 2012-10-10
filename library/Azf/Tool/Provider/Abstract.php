<?php

require_once __DIR__."/../CopyBuilder.php";
require_once __DIR__.'/../DirectoryBuilder.php';

class Azf_Tool_Provider_Abstract  extends Zend_Tool_Framework_Provider_Abstract {
   
    
    
    protected $_directoryBuilder;
    protected $_copyBuilder;
    
    
    /**
     * 
     * @return Azf_Tool_DirectoryBuilder
     */
    protected function _getDirectoryBuilder($basePath=null) {
        if(!$this->_directoryBuilder){
            $this->_directoryBuilder = new Azf_Tool_DirectoryBuilder();
        }
        
        if($basePath!==null) {
            $this->_directoryBuilder->createBasepath($basePath);
        }
        
        return $this->_directoryBuilder;
    }

    /**
     * 
     * @return Azf_Tool_CopyBuilder
     */
    protected function _getCopyBuilder($dstPath = null) {
        if(!$this->_copyBuilder){
            $this->_copyBuilder = new Azf_Tool_CopyBuilder();
        }
        $this->_copyBuilder->setBaseTemplatePath($this->_getResourcePath());
        if($dstPath!==null){
            $this->_copyBuilder->setBaseDstPath($dstPath);
        }
        
        return $this->_copyBuilder;
    }

    
    
    /**
     * 
     * @return string
     */
    protected function getCwd() {
        return getcwd();
    }
    
    
    /**
     * 
     * @param string $resource
     * @return string|null
     */
    protected function _getResourcePath($resource=".") {
        return realpath(__DIR__."/resources/$resource");
    }
    
    
    /**
     * 
     * @param string $module
     * @return boolean
     */
    protected function _phpModuleExists($module) {
        $modulePath = $this->_getPhpModulePath($module);
        return file_exists($modulePath);
    }
    
    protected function _getDescriptorsBasePath() {
        return $this->getCwd()."/application/configs/descriptor";
    }
    
    protected function _getExtensionPluginDescriptorsBasePath() {
        return $this->_getDescriptorsBasePath()."/plugin/extension";
    }
    
    protected function _getContentPluginDescriptorsBasePath() {
        return $this->_getDescriptorsBasePath()."/plugin/content";
    }
    
    /**
     * @return string
     */
    protected function _getJsBasePath() {
        return $this->getCwd()."/public/js/lib/azfcms";
    }
    
    protected function _getJsModulesBasePath() {
        return $this->_getJsBasePath()."/module";
    }
    
    protected function _getJsModuleBasePath($module) {
        return $this->_getJsModulesBasePath()."/$module";
    }
    
    protected function _getJsExtensionPluginsBasePath($module) {
        return $this->_getJsModuleBasePath($module)."/extensionPlugin";
    }
    
    protected function _getJsExtensionPluginPath($module,$name) {
        return $this->_getJsExtensionPluginsBasePath($module)."/".$name;
    }
    
    protected function _getJsContentPluginsBasePath($module) {
        return $this->_getJsModuleBasePath($module)."/contentPlugin";
    }
    
    protected function _getJsConfigurationPluginsBasePath($module) {
        return $this->_getJsModuleBasePath($module)."/configurationPlugin";
    }
    
    protected function _getJsConfigurationPluginBasePath($module,$name) {
        return $this->_getJsModuleBasePath($module)."/configurationPlugin/$name";
    }
    
    protected function _getContentPluginsBasePath($module) {
        return $this->_getJsModuleBasePath($module)."/contentPlugin";
    }
    
    protected function _getContentPluginBasePath($module, $name) {
        return $this->_getContentPluginsBasePath($module)."/$name";
    }
    
    protected function _getTestJsModulesBasePath() {
        return $this->getCwd()."/public/js/lib/azfcms/tests/module";
    }
    
    
    protected function _getTestJsModulePath($module) {
        return $this->_getTestJsModulesBasePath()."/$module";
    }
    
    protected function _getTestJsContentPluginBasePath($module, $name) {
        return $this->_getTestJsModulePath($module)."/contentPlugin/$name";
    }
    
    protected function _getTestJsExtensionPluginBasePath($module) {
        return $this->_getTestJsModulePath($module)."/extensionPlugin";
    }
    
    protected function _getTestJsExtensionPluginPath($module, $name) {
        return $this->_getTestJsExtensionPluginBasePath($module)."/$name";
    }
    
    
    protected function _getTestJsConfigurationPluginBasePaths($module) {
        return $this->_getTestJsModulePath($module)."/configurationPlugin";
    }
    
    
    protected function _getTestJsConfigurationPluginBasePath($module, $name) {
        return $this->_getTestJsModulePath($module)."/configurationPlugin/$name";
    }
    
    protected function _getJsConfigurationBootstrapPath() {
        return $this->_getJsBasePath()."/bootstrap/configuration";
    }
    
    protected function _getPhpApplicationPath() {
        return $this->getCwd()."/application";
    }
    
    protected function _getPhpModulePath($module) {
        if($module=="default"){
            return $this->_getPhpApplicationPath();
        } else {
            return $this->_getPhpApplicationPath()."/modules/$module";
        }
    }
    
    
    public function _getPhpExtensionPluginBasePath($module) {
        return $this->_getPhpModulePath($module)."/plugins/Extension";
    }
    
    public function _getPhpExtensionPluginViewBasePath($module) {
        return $this->_getPhpModulePath($module)."/views/pluginScripts";
    }
    
    public function _getPhpExtensionPluginViewPath($module, $name) {
        return $this->_getPhpExtensionPluginViewBasePath($module)."/".$name;
    }
    
    protected function _getPhpContentPluginPath($module, $name) {
        return $this->_getPhpModulePath($module)."/controllers";
    }
    
    protected function _getPhpContentPluginViewPath($module, $name) {
        return $this->_getPhpModulePath($module)."/views/scripts/$name";
    }
    
    /**
     * 
     * @param string $module
     * @return boolean
     */
    protected function moduleExists($module) {
        return $this->_jsModuleExists($module)&&$this->_phpModuleExists($module);
    }
    
    /**
     * 
     * @param string $module
     * @return boolean
     */
    protected function _jsModuleExists($module) {
        $modulePath = $this->_getJsModuleBasePath($module);
        return file_exists($modulePath);
    }
    
    protected function _writeln($text){
        $this->_registry->getResponse()->appendContent($text."\n");
    }
    
    
    protected function _writeBuilderAndClear(Azf_Tool_AbstractBuilder $builder) {
        $this->_writeln($builder->getLogLinesAndClean());
    }
   
    
    
}
