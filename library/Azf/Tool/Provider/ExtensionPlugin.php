<?php

require_once 'AbstractPlugin.php';
/**
 * Description of Module
 *
 * @author antun
 */
class Azf_Tool_Provider_ExtensionPlugin extends Azf_Tool_Provider_AbstractPlugin {
    
    static $extensionPluginLayout = array('view','controller','view/templates','resource/i18n/nls');
    static $extensionPluginTestLayout = array('view','controller');
    
    public function getComponentName() {
        return "extensionPlugin";
    }

    protected function _prepareObject($module, $name, $resource = "") {
        parent::_prepareObject($module, $name, $resource);
        $this->_baseJsPath = $this->_getJsExtensionPluginPath($module, $name);
        $this->_baseJsTestPath = $this->_getTestJsExtensionPluginPath($module, $name);
    }
    
    protected function _createLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_baseJsPath);
        $dirBuilder->createLayout(self::$extensionPluginLayout);
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _createTestLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_getTestJsExtensionPluginPath($this->_module, $this->_name));
        $dirBuilder->createLayout(self::$extensionPluginTestLayout);
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _createClassFiles() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseTemplatePath($this->_getResourcePath("."));
        $copyBuilder->setBaseDstPath($this->_baseJsPath);
        
        $copyBuilder->copyTemplate("ExtensionPluginController.js", "controller/$this->_ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ExtensionPluginView.js", "view/$this->_ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ExtensionPluginTemplate.html", "view/templates/$this->_ucName.html", $this->_templateArgs);
        $copyBuilder->copyTemplate("Defaulti18n.js","resource/i18n/nls/".ucfirst($this->_name).".js",$this->_templateArgs);
        
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    
    protected function _createDescriptor() {
        $copyBuilder = $this->_getCopyBuilder($this->_getExtensionPluginDescriptorsBasePath());
        
        $copyBuilder->copyTemplate("ExtensionPluginDescriptor.xml", $this->_ucModule.$this->_ucName.".xml", $this->_templateArgs);
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createTestFiles() {
        $copyBuilder = $this->_getCopyBuilder($this->_getTestJsExtensionPluginPath($this->_module, $this->_name));
        
        $copyBuilder->copyTemplate("ExtensionPluginViewTest.html", "view/".$this->_ucName.".html", $this->_templateArgs);
        
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createPhpPlugin() {
        $copyBuilder = $this->_getCopyBuilder($this->_getPhpExtensionPluginBasePath($this->_module));
        
        $copyBuilder->copyTemplate("ExtensionPluginPhpScript.php", $this->_ucName.".php", $this->_templateArgs);
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createPhpViewScript() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createFullPathDirectory($this->_getPhpExtensionPluginViewPath($this->_module, $this->_name));
        
        $copyBuilder = $this->_getCopyBuilder($this->_getPhpExtensionPluginViewPath($this->_module, $this->_name));
        
        $copyBuilder->copyTemplate("ExtensionPluginView.phtml", "index.phtml", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($dirBuilder);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    public function create($module, $name) {
        $this->_prepareObject($module, $name);
        
        if($this->_jsModuleExists($module)==false){
            $this->_writeln("Given module does not exists");
            return ;
        }
        
        if(file_exists($this->_baseJsPath)){
            $this->_writeln("Extension plugin already exists");
            return;
        }
        
        $this->_createLayout();
        $this->_createTestLayout();
        $this->_createClassFiles();
        $this->_createTestFiles();
        $this->_createDescriptor();
        $this->_createPhpPlugin();
        $this->_createPhpViewScript();
        
        $this->_writeln("Extension plugin is created");
    }
    
    
    protected function _deleteExtensionPluginDir() {
        $dirBuilder= $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_baseJsPath);
        
        if(!$dirBuilder->basePathExists()){
            return;
        }
        
        $dirBuilder->destroyRecursive();
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    public function _deleteTestExtensionPluginDir() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_baseJsTestPath);
        
        $dirBuilder->destroyRecursive();
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _deleteDescriptor() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_getExtensionPluginDescriptorsBasePath());
        
        $dirBuilder->delete($this->_ucModule.$this->_ucName.".xml");
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _deletePhpPlugin() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_getPhpExtensionPluginBasePath($this->_module));
        
        $dirBuilder->delete($this->_ucName.".php");
        $this->_writeBuilderAndClear($dirBuilder);
        
    }
    
    public function delete($module, $name) {
        if($this->_jsModuleExists($module)==false){
            return;
        }
        
        $this->_prepareObject($module, $name);
        
       $this->_deleteExtensionPluginDir();
       $this->_deleteTestExtensionPluginDir();
       $this->_deleteDescriptor();
       $this->_deletePhpPlugin();
        
       $this->_writeln("Extension plugin $name is deleted");
    }


}

