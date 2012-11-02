<?php


/**
 * Description of AzfModule
 *
 * @author antun
 */
class Azf_Tool_Provider_AzfModule extends Azf_Tool_Provider_Abstract{
     
    static $jsModuleDirLayout = array(
        'configurationPlugin','contentPlugin','controller','extensionPlugin',
        'view','store','generator'
    );
    
    static $phpModuleDirLayout = array(
        'controllers','filters','models','models/DbTable',
        'plugins','plugins/Extension','resolvers','rests',
        'views','views/helpers','views/layouts','views/pluginScripts',
        'views/scripts'
    );
    
    protected function _getLayout($module) {
        return array(
            $this->_getJsModuleBasePath($module)=>self::$jsModuleDirLayout,
            $this->_getTestJsModulePath($module)=>self::$jsModuleDirLayout,
            $this->_getPhpModulePath($module)=>self::$phpModuleDirLayout
        );
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
    
    
    public function create($module) {
        $this->_createLayoutV2($this->_getLayout($module));
        $this->_createPhpBootstrap($module);
        
    }
    
    public function delete($module) {
        $this->_deleteLayoutV2($this->_getLayout($module));
    }
}