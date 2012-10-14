<?php

require_once "AbstractPlugin.php";
/**
 * Description of ContentPlugin
 *
 * @author antun
 */
class Azf_Tool_Provider_ContentPlugin extends Azf_Tool_Provider_AbstractPlugin {
    
    static $_jsDirectoryLayout = array(
        'controller','view','view/templates',
        'resource/i18n/nls'
    );
    
    static $_jsDirectoryTestLayout = array(
        'controller','view'
    );
    
    
    protected $_phpBasePath;
    protected $_phpViewScriptsBasePath;
    protected $_descriptorBasePath;
    
    public function getComponentName() {
        return "contentPlugin";
    }
    
    protected function _prepareObject($module, $name, $resource = "") {
        parent::_prepareObject($module, $name, $resource);
        $this->_baseJsPath = $this->_getContentPluginBasePath($module,$name);
        $this->_baseJsTestPath = $this->_getTestJsContentPluginBasePath($module,$name);
        $this->_phpBasePath = $this->_getPhpContentPluginPath($module, $name);
        $this->_phpViewScriptsBasePath = $this->_getPhpContentPluginViewPath($module, $name);
        $this->_descriptorBasePath = $this->_getContentPluginDescriptorsBasePath();
    }
    
    
    protected function _createContentPluginDirectoryLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_baseJsPath);
        $dirBuilder->createLayout(self::$_jsDirectoryLayout);
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsScripts() {
        $copyBuilder = $this->_getCopyBuilder();
        
        $copyBuilder->setBaseDstPath($this->_baseJsPath);
        $copyBuilder->copyTemplate("ContentPluginView.js", "view/".$this->_ucName.".js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ContentPluginController.js","controller/".$this->_ucName.".js",$this->_templateArgs);
        $copyBuilder->copyTemplate("ContentPluginTemplate.html","view/templates/".$this->_ucName.".html",$this->_templateArgs);
        $copyBuilder->copyTemplate("Defaulti18n.js","resource/i18n/nls/".$this->_ucName.".js",$this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createJsTestLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_baseJsTestPath);
        $dirBuilder->createLayout(self::$_jsDirectoryTestLayout);
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsTestScripts() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_baseJsTestPath);
        $copyBuilder->copyTemplate("ContentPluginViewTest.html", "view/".$this->_ucName.".html", $this->_templateArgs);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createPhpDescriptor() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_descriptorBasePath);
        
        $copyBuilder->copyTemplate("ContentPluginDescriptor.xml", $this->_ucModule.$this->_ucName.".xml", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createPhpScripts() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_phpBasePath);
        
        $copyBuilder->copyTemplate("ContentPluginController.php", ucfirst(strtolower($this->_name))."Controller.php", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createPhpViewScripts() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_phpViewScriptsBasePath);
        
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_phpViewScriptsBasePath);
        $copyBuilder->copyTemplate("ContentPluginViewScript.phtml", "render.phtml", $this->_templateArgs);
        
        $this->_writeBuilderAndClear($dirBuilder);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    public function create($module, $name) {
        $this->_prepareObject($module, $name);

        if($this->_getDirectoryBuilder()->fullPathExists($this->_baseJsPath)){
            $this->_writeln("Module already exists");
            return;
        }
        $this->_createContentPluginDirectoryLayout();
        $this->_createJsScripts();
        $this->_createJsTestLayout();
        $this->_createJsTestScripts();
        $this->_createPhpDescriptor();
        $this->_createPhpScripts();
        $this->_createPhpViewScripts();
        
        $this->_writeln("\nContent plugin $name is created");
        
    }
    
    public function _deleteJsContentPlugin() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_baseJsPath);
        $dirBuilder->destroyRecursive();
        
        $dirBuilder->setBasePath($this->_baseJsTestPath);
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
        
    }
    
    protected function _deleteContentPluginDescriptor() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_descriptorBasePath);
        
        $dirBuilder->delete($this->_ucModule.$this->_ucName.".xml");
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _deletePhpController() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_phpBasePath);
        $dirBuilder->delete(ucfirst(strtolower($this->_name))."Controller.php");
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _deletePhpViewScripts() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_phpViewScriptsBasePath);
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    public function delete($module, $name) {
        $this->_prepareObject($module, $name);
        
        
        $this->_deleteJsContentPlugin();
        $this->_deleteContentPluginDescriptor();
        $this->_deletePhpController();
        $this->_deletePhpViewScripts();
        
        $this->_writeln("\nContent plugin $name is deleted");
    }
}

