<?php

/**
 * Description of store
 *
 * @author antun
 */
class Azf_Tool_Provider_ServerStore extends Azf_Tool_Provider_AbstractPlugin{
    protected $_basePhpModelPath;
    protected $_basePhpFilterPath;
    protected $_basePhpResolverPath;



    public function getComponentName() {
        return "";
    }

    protected function _prepareObject($module, $name, $resource = "") {
        parent::_prepareObject($module, $name, $resource);
        $this->_basePhpModelPath = $this->_getPhpDbModelPath($module);
        $this->_basePhpFilterPath = $this->_getPhpFilterPath($module);
        $this->_basePhpResolverPath = $this->_getPhpResolverPath($module);
    }
    
    
    /**
     *  This method will copy all provided templates into base path
     * 
     * Example structure:
     * array(
     *  'base/path/1'=>array(
     *      array('Resource1.js','DstName1.js'),
     *      array('Resource2.js','view/DstName2.js')
     *  ),
     *  'base/path/2'=>array(
     *      array('Resource1.js','DstName1.js'),
     *      array('Resource2.js','view/DstName2.js')
     *  )
     * )
     * @param type $templates
     */
    protected function _copyTemplates($templates) {
        $copyBuilder = $this->_getCopyBuilder();
        
        foreach($templates as $basePath => $pathTemplates){
            $copyBuilder->setBaseDstPath($basePath);
            foreach($pathTemplates as $template){
                $copyBuilder->copyTemplate($template[0], $template[1], $this->_templateArgs);
            }
        }
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _deleteTemplates($templates) {
        $dirBuilder = $this->_getDirectoryBuilder();
        
        foreach($templates as $basePath => $pathTemplates){
            $dirBuilder->setBasePath($basePath);
            
            foreach($pathTemplates as $template){
                $dirBuilder->delete($template[1]);
            }
        }
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    protected function _getStoreTemplates() {
        return array(
            $this->_basePhpModelPath=>array(
                array('DefaultPhpDbModel.php',$this->_ucName.".php")
            ),
            $this->_basePhpFilterPath=>array(
                array('DefaultPhpFilter.php',$this->_ucName.".php")
            ),
            $this->_basePhpResolverPath=>array(
                array('DefaultPhpResolver.php',$this->_ucName.".php")
            )
        );
    }
    
    
    public function create($module, $name) {
        $this->_prepareObject($module, $name);
        
        $this->_copyTemplates($this->_getStoreTemplates());
        
        $this->_writeln("Store is created");
    }
    
    public function delete($module, $name) {
        $this->_prepareObject($module, $name);
        
        $this->_deleteTemplates($this->_getStoreTemplates());
        
        $this->_writeln("Store is deleted");
    }
    
    
    
}
