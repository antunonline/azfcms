<?php


/**
 * Description of AzfModule
 *
 * @author antun
 */
class Azf_Tool_Provider_AzfModule extends Azf_Tool_Provider_Abstract{
     
    static $jsModuleDirLayout = array(
        'configurationPlugin','contentPlugin','controller','extensionPlugin',
        'view','store'
    );
    
    static $phpModuleDirLayout = array(
        'controllers','filters','models','models/DbTable',
        'plugins','plugins/Extension','resolvers','rests',
        'views','views/helpers','views/layouts','views/pluginScripts',
        'views/scripts'
    );
    
    /**
     * 
     * @param string $module
     */
    protected function _createJsModule($module) {
        $dirBuilder = $this->_getDirectoryBuilder();
        $modulePath = $this->_getJsModuleBasePath($module);
        
        $dirBuilder->createBasepath($modulePath);
        $dirBuilder->createLayout(self::$jsModuleDirLayout);
    }
    
    protected function _createJsTestModule($module) {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->createBasepath($this->_getTestJsModulePath($module));
        $dirBuilder->createLayout(self::$jsModuleDirLayout);
    }
    
    /**
     * 
     * @param string $module
     */
    protected function _createPhpModule($module) {
        $dirBuilder = $this->_getDirectoryBuilder();
        $modulePath= $this->_getPhpModulePath($module);
        
        $dirBuilder->createBasepath($modulePath);
        $dirBuilder->createLayout(self::$phpModuleDirLayout);
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    protected function _createPhpBootstrap($module) {
        $copyBuilder =$this->_getCopyBuilder();
        $copyBuilder->setBaseTemplatePath($this->_getResourcePath());
        $copyBuilder->setBaseDstPath($this->_getPhpModulePath($module));
        
        $moduleName = strtolower($module)=="default"?"Application":ucfirst(strtolower($module));
        $templateArgs = array(
            'module'=>  strtolower($module),
            'ucModule' => $moduleName
        );
        
        $copyBuilder->copyTemplate("PhpModuleBootstrap.php", "Bootstrap.php", $templateArgs);
        
        $this->_writeln($copyBuilder->getLogLinesAndClean());
    }
    
    public function createPhp($module) {
        $this->_createPhpModule($module);
        $this->_createPhpBootstrap($module);
    }
    
    
    protected function _deletePhpModuleDirectory($module) {
        $dirBuilder = $this->_getDirectoryBuilder();
        $dirBuilder->setBasePath($this->_getPhpModulePath($module));
        
        $dirBuilder->destroyRecursive();
    }
    
    public function deletePhp($module) {
        if($this->_getDirectoryBuilder()->fullPathExists($this->_getPhpModulePath($module))==false){
            $this->_writeln("PHP module does not exists");
            return;
        }
        
        $this->_deletePhpModuleDirectory($module);
        $this->_writeln($this->_getDirectoryBuilder()->getLogLinesAndClean());
        $this->_writeln("Done");
    }
    
    public function create($module) {
        $phpModulePath = $this->_getPhpModulePath($module);
        $dirBuilder = $this->_getDirectoryBuilder();
        
        if($dirBuilder->fullPathExists($phpModulePath)){
            $this->_registry->getResponse()->appendContent("PHP module already exists");
            return;
        }
        $this->createPhp($module);
        $this->createJs($module);
        
        $this->_writeln($dirBuilder->getLogLinesAndClean());
    }
    
    public function createJs($module) {
        $jsModulePath = $this->_getJsModuleBasePath($module);
        $dirBuilder = $this->_getDirectoryBuilder();
        
        if($dirBuilder->fullPathExists($jsModulePath)){
            $this->_writeln("JS module already exists");
            return;
        }
        
        $this->_createJsModule($module);
        $this->_createJsTestModule($module);
        
        $this->_writeln($dirBuilder->getLogLines());
        $this->_writeln(("JS module created"));
    }
    
    public function deleteJs($module) {
        $modulePath = $this->_getJsModuleBasePath($module);
        $testModulePath = $this->_getTestJsModulePath($module);
        $dirBuilder = $this->_getDirectoryBuilder();
        
        if($dirBuilder->fullPathExists($modulePath)==false){
            $this->_writeln("Module does not exists");
        } else {
            $dirBuilder->destroyRecursiveFullPath($modulePath);
            $dirBuilder->destroyRecursiveFullPath($testModulePath);
            $this->_writeln($dirBuilder->getLogLines());
            $this->_writeln("JS Module is deleted");
        }
    }
    
    
    public function delete($module) {
        $this->deleteJs($module);
        $this->deletePhp($module);
    }
}