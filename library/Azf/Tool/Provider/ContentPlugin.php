<?php

require_once __DIR__."/Abstract.php";
/**
 * Description of ContentPlugin
 *
 * @author antun
 */
class Azf_Tool_Provider_ContentPlugin extends Azf_Tool_Provider_Abstract {
    
    static $_jsDirectoryLayout = array(
        'controller','view','view/templates'
    );
    
    static $_jsTestDirectoryLayout = array(
        'controller','view'
    );
    
    protected $_module;
    protected $_name;
    protected $_jsBasePath;
    protected $_testJsBasePath;
    protected $_phpBasePath;
    protected $_phpViewScriptsBasePath;
    protected $_templateArgs;
    protected $_descriptorBasePath;
    
    
    protected function _createContentPluginDirectoryLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_jsBasePath);
        $dirBuilder->createLayout(self::$_jsDirectoryLayout);
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsScripts() {
        $copyBuilder = $this->_getCopyBuilder();
        
        $copyBuilder->setBaseDstPath($this->_jsBasePath);
        $copyBuilder->copyTemplate("ContentPluginView.js", "view/".ucfirst($this->_name).".js", $this->_templateArgs);
        $copyBuilder->copyTemplate("ContentPluginController.js","controller/".ucfirst($this->_name).".js",$this->_templateArgs);
        $copyBuilder->copyTemplate("ContentPluginTemplate.html","view/templates/".ucfirst($this->_name).".html",$this->_templateArgs);
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createJsTestLayout() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_testJsBasePath);
        $dirBuilder->createLayout(self::$_jsTestDirectoryLayout);
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _createJsTestScripts() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_testJsBasePath);
        $copyBuilder->copyTemplate("ContentPluginViewTest.html", "view/".ucfirst($this->_name).".html", $this->_templateArgs);
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _createPhpDescriptor() {
        $copyBuilder = $this->_getCopyBuilder();
        $copyBuilder->setBaseDstPath($this->_descriptorBasePath);
        
        $copyBuilder->copyTemplate("ContentPluginDescriptor.xml", ucfirst($this->_name).".xml", $this->_templateArgs);
        
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
    
    
    protected function _setupObject($module, $name) {
        $this->_module = $module;
        $this->_name = $name;
        $this->_jsBasePath = $this->_getContentPluginBasePath($module,$name);
        $this->_testJsBasePath = $this->_getTestJsContentPluginBasePath($module,$name);
        $this->_phpBasePath = $this->_getPhpContentPluginPath($module, $name);
        $this->_phpViewScriptsBasePath = $this->_getPhpContentPluginViewPath($module, $name);
        $this->_descriptorBasePath = $this->_getContentPluginDescriptorsBasePath();
        $this->_templateArgs =array(
            'module'=>strtolower($module),
            'ucModule'=>  ucfirst(strtolower($module)),
            'name'=>$name,
            'ucName'=>  ucfirst($name)
        );
    }
    
    public function create($module, $name) {
        $this->_setupObject($module, $name);

        if($this->_getDirectoryBuilder()->fullPathExists($this->_jsBasePath)){
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
        $dirBuilder->setBasePath($this->_jsBasePath);
        $dirBuilder->destroyRecursive();
        
        $dirBuilder->setBasePath($this->_testJsBasePath);
        $dirBuilder->destroyRecursive();
        
        $this->_writeBuilderAndClear($dirBuilder);
        
    }
    
    protected function _deleteContentPluginDescriptor() {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_descriptorBasePath);
        
        $dirBuilder->delete(ucfirst($this->_name).".xml");
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
        $this->_setupObject($module, $name);
        
        
        $this->_deleteJsContentPlugin();
        $this->_deleteContentPluginDescriptor();
        $this->_deletePhpController();
        $this->_deletePhpViewScripts();
        
        $this->_writeln("\nContent plugin $name is deleted");
    }
}

