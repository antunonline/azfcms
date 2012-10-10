<?php

require_once "Abstract.php";
/**
 * Description of Configuratin
 *
 * @author antun
 */
class Azf_Tool_Provider_Configuration extends Azf_Tool_Provider_Abstract{
    
    protected static $_jsDirectoryLayout = array(
        'controller','view','view/templates','resource/nls'
    );
    
    protected static $_jsDirectoryTestLayout = array(
        'view','controller'
    );
    
    protected $_module;
    protected $_ucModule;
    protected $_name;
    protected $_ucName;
    protected $_baseJsPath;
    protected $_baseJsTestPath;
    protected $_baseBootstrapPath;
    protected $_templateArgs;
    
    
    protected function _prepareObject($module, $name) {
        $this->_module = $module;
        $this->_ucModule = ucfirst($module);
        $this->_name = $name;
        $this->_ucName = ucfirst($this->_name);
        $this->_baseJsPath = $this->_getJsConfigurationPluginBasePath($module,$name);
        $this->_baseJsTestPath = $this->_getTestJsConfigurationPluginBasePath($module,$name);
        $this->_baseBootstrapPath = $this->_getJsConfigurationBootstrapPath();
        $this->_templateArgs = array(
            'name'=>$name,
            'ucName'=>$this->_ucName,
            'module'=>$this->_module,
            'ucModule'=>$this->_ucModule
        );
    }
    
    protected function _createDirectoryLayout() {
        $dirBuilder = $this->_getDirectoryBuilder($this->_baseJsPath);
        $dirBuilder->createLayout(self::$_jsDirectoryLayout);
        $dirBuilder->createDirectory("resource/i18n/nls/".$this->_ucName);
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsScripts() {
        $copyBuilder = $this->_getCopyBuilder($this->_baseJsPath);

        $copyBuilder->copyTemplate("ConfigurationPluginView.js", "view/$this->_ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ConfigurationPluginController.js", "controller/$this->_ucName.js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ConfigurationPluginViewTemplate.html", "view/templates/$this->_ucName.html", $this->_templateArgs);
        $copyBuilder->copyTemplate("ConfigurationPluginI18n.js", "resource/i18n/nls/$this->_ucName.js", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createJsTestDirectoryLayout() {
        $dirBuilder = $this->_getDirectoryBuilder($this->_baseJsTestPath);
        $dirBuilder->createLayout(self::$_jsDirectoryTestLayout);
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsTestScripts() {
        $copyBuilder = $this->_getCopyBuilder($this->_baseJsTestPath);
        
        $copyBuilder->copyTemplate("ConfigurationPluginViewTest.html", "view/$this->_ucName.html", $this->_templateArgs);
        $copyBuilder->copyTemplate("ConfigurationPluginControllerTest.html", "controller/$this->_ucName.html", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    
    protected function _createBootstrapScript() {
        $copyBuilder = $this->_getCopyBuilder($this->_baseBootstrapPath);
        $copyBuilder->copyTemplate("ConfigurationPluginBootstrapScripts.js", "$this->_ucModule{$this->_ucName}.js", $this->_templateArgs);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    public function create($module, $name) {
        $this->_prepareObject($module, $name);
        
        if($this->_getDirectoryBuilder()->fullPathExists($this->_baseJsPath)){
            $this->_writeln("Content plugin already exists!");
            return ;
        }
        
        $this->_createDirectoryLayout();
        $this->_createJsScripts();
        $this->_createJsTestDirectoryLayout();
        $this->_createJsTestScripts();
        $this->_createBootstrapScript();
        
        $this->_writeln("Configuration plugin is created");
    }
    
    protected function _deletePluginDirectory() {
        
        $dirBuilder = $this->_getDirectoryBuilder($this->_baseJsPath);
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _deleteJsTestDirectoryLayout() {
        $dirBuilder = $this->_getDirectoryBuilder($this->_baseJsTestPath);
        
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _deleteJsTestScripts() {
        $dirBuilder = $this->_getDirectoryBuilder($this->_baseJsTestPath);
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _deleteBootstrapScript() {
        $directoryBuilder = $this->_getDirectoryBuilder($this->_baseBootstrapPath);
        $directoryBuilder->delete("$this->_ucModule{$this->_ucName}.js");
        $this->_writeBuilderAndClear($directoryBuilder);
    }


    public function delete($module, $name) {
        $this->_prepareObject($module, $name);
        $this->_deletePluginDirectory();
        $this->_deleteJsTestDirectoryLayout();
        $this->_deleteJsTestScripts();
        $this->_deleteBootstrapScript();
        
        $this->_writeln("Configuration plugin is deleted");
    }
}

