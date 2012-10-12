<?php

require_once 'Abstract.php';
/**
 * Description of Module
 *
 * @author antun
 */
class Azf_Tool_Provider_ExtensionPlugin extends Azf_Tool_Provider_Abstract {
    
    static $extensionPluginLayout = array('view','controller','view/templates');
    static $extensionPluginTestLayout = array('view','controller');
    protected $_module;
    protected $_name;
    protected $_basePath;
    protected $_templateArgs = array();
    public $_testBasepath = "";
    
    protected function _createLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_basePath);
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
        $copyBuilder->setBaseDstPath($this->_basePath);
        $ucName = ucfirst($this->_name);
        
        
        $copyBuilder->copyTemplate("ExtensionPluginController.js", "controller/$ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ExtensionPluginView.js", "view/$ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ExtensionPluginTemplate.html", "view/templates/$ucName.html", $this->_templateArgs);
        
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    
    protected function _createDescriptor() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_getExtensionPluginDescriptorsBasePath());
        $copyBuilder->setBaseTemplatePath($this->_getResourcePath());
        
        $copyBuilder->copyTemplate("ExtensionPluginDescriptor.xml", ucfirst($this->_name).".xml", $this->_templateArgs);
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createTestFiles() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseTemplatePath($this->_getResourcePath("."));
        $copyBuilder->setBaseDstPath($this->_getTestJsExtensionPluginPath($this->_module, $this->_name));
        
        $copyBuilder->copyTemplate("ExtensionPluginViewTest.html", "view/".ucfirst($this->_name).".html", $this->_templateArgs);
        
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createPhpPlugin() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseTemplatePath($this->_getResourcePath());
        $copyBuilder->setBaseDstPath($this->_getPhpExtensionPluginBasePath($this->_module));
        
        $copyBuilder->copyTemplate("ExtensionPluginPhpScript.php", ucfirst($this->_name).".php", $this->_templateArgs);
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    protected function _createPhpViewScript() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createFullPathDirectory($this->_getPhpExtensionPluginViewPath($this->_module, $this->_name));
        
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_getPhpExtensionPluginViewPath($this->_module, $this->_name));
        
        $copyBuilder->copyTemplate("ExtensionPluginView.phtml", "index.phtml", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($dirBuilder);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    public function create($module, $name) {
        $this->_module = $module;
        $this->_name = $name;
        $this->_basePath = $this->_getJsExtensionPluginPath($module, $name);
        $this->_templateArgs = array(
            'name'=>$this->_name,
            'ucName'=>  ucfirst($name),
            'module'=>$module,
            'ucModule'=>  ucfirst($module)
        );
        if($this->_jsModuleExists($module)==false){
            $this->_writeln("Given module does not exists");
            return ;
        }
        
        if(file_exists($this->_basePath)){
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
        $dirBuilder->setBasePath($this->_basePath);
        
        if(!$dirBuilder->basePathExists()){
            return;
        }
        
        $dirBuilder->destroyRecursive();
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    public function _deleteTestExtensionPluginDir() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_testBasepath);
        
        $dirBuilder->destroyRecursive();
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _deleteDescriptor() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_getExtensionPluginDescriptorsBasePath());
        
        $dirBuilder->delete(ucfirst($this->_name).".xml");
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _deletePhpPlugin() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_getPhpExtensionPluginBasePath($this->_module));
        
        $dirBuilder->delete(ucfirst($this->_name).".php");
        $this->_writeBuilderAndClear($dirBuilder);
        
    }
    
    public function delete($module, $name) {
        if($this->_jsModuleExists($module)==false){
            return;
        }
        
        $this->_module = $module;
        $this->_name = $name;
        $this->_basePath = $this->_getJsExtensionPluginPath($module, $name);
        $this->_testBasepath = $this->_getTestJsExtensionPluginPath($module, $name);
        
       $this->_deleteExtensionPluginDir();
       $this->_deleteTestExtensionPluginDir();
       $this->_deleteDescriptor();
       $this->_deletePhpPlugin();
        
       $this->_writeln("Extension plugin $name is deleted");
    }


}

