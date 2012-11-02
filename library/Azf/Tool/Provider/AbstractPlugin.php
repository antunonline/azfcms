<?php

require_once 'Abstract.php';

/**
 * Description of AbstractPlugin
 *
 * @author antun
 */
class Azf_Tool_Provider_AbstractPlugin extends Azf_Tool_Provider_Abstract{
    
    protected $_module;
    protected $_ucModule;
    protected $_name;
    protected $_ucName;
    protected $_resource;
    protected $_ucResource;
    protected $_baseJsPath;
    protected $_baseJsTestPath;
    protected $_baseBootstrapPath;
    protected $_templateArgs;
    
    
    protected function _prepareObject($module, $name, $resource = "") {
        $this->_module = $module;
        $this->_ucModule = ucfirst($module);
        $this->_name = $name;
        $this->_ucName = ucfirst($this->_name);
        $this->_resource = $resource;
        $this->_ucResource = ucfirst($resource);
        $this->_templateArgs = array(
            'name'=>$name,
            'ucName'=>$this->_ucName,
            'module'=>$this->_module,
            'ucModule'=>$this->_ucModule,
            'resource'=>$this->_resource,
            'ucResource'=>$this->_ucResource,
            'componentName'=>$this->getComponentName()
        );
    }
    
    
    public function getComponentName(){
        throw new RuntimeException("getComponentName method must be overriden");
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
    protected function _copyTemplatesV2($templates) {
        $copyBuilder = $this->_getCopyBuilder();
        
        foreach($templates as $basePath => $pathTemplates){
            $copyBuilder->setBaseDstPath($basePath);
            
            foreach($pathTemplates as $template){
                $copyBuilder->copyTemplate($template[0], $template[1], $this->_templateArgs);
            }
        }
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    
    /**
     *  This method will eventually be removed, use _copyTemplatesV2 method instead
     * @param type $copyTemplates
     * @param type $copyTestTemplates
     */
    protected function _copyTemplates($copyTemplates, $copyTestTemplates) {
        $copyBuilder = $this->_getCopyBuilder($this->_baseJsPath);
        foreach($copyTemplates as $template){
            $copyBuilder->copyTemplate($template[0], $template[1], $this->_templateArgs);
        }
        
        $copyBuilder->setBaseDstPath($this->_baseJsTestPath);
        foreach($copyTestTemplates as $template){
            $copyBuilder->copyTemplate($template[0], $template[1], $this->_templateArgs);
        }
        
        $this->_writeBuilderAndClear($copyBuilder);
    }
    
    protected function _deleteTemplates($jsTemplates, $jsTestTemplates) {
        $dirBuilder = $this->_getDirectoryBuilder();
        
        $dirBuilder->setBasePath($this->_baseJsPath);
        foreach($jsTemplates as $template){
            $dirBuilder->delete($template[1]);
        }
        
        $dirBuilder->setBasePath($this->_baseJsTestPath);
        foreach($jsTestTemplates as $template){
            $dirBuilder->delete($template[1]);
        }
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    
    /**
     *  This method will delete all provided templates and directories
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
    protected function _deleteTemplatesV2($templates) {
        $dirBuilder = $this->_getDirectoryBuilder();
        
        foreach($templates as $basePath => $pathTemplates){
            $dirBuilder->setBasePath($basePath);
            
            foreach($pathTemplates as $template){
                $dirBuilder->delete($template[1]);
            }
        }
        
        $this->_writeBuilderAndClear($dirBuilder);
    }
    
    
    
    
    protected function _getDefaultFormTemplates() {
        $copyTemplates = array(
                array("DefaultForm.js","view/{$this->_ucResource}Form.js"),
                array("DefaultForm.html","view/templates/{$this->_ucResource}Form.html")    
        );
        
        $copyTestTemplates = array(
            array("DefaultFormTest.html","view/{$this->_ucResource}Form.html")
        );
            
        return array($copyTemplates,$copyTestTemplates);
    }
    
    protected function _getGridTemplates() {
        $baseTemplates = array(
            array("DefaultGrid.js","view/{$this->_ucResource}Grid.js"),
            array("DefaultGridTemplate.html","view/templates/{$this->_ucResource}Grid.html")
        );
        $testTempaltes = array(
            array("DefaultGridTest.html","view/{$this->_ucResource}Grid.html")
        );
        return array($baseTemplates,$testTempaltes);
    }
    
    


    public function createForm($module, $name, $form) {
        $this->_prepareObject($module, $name, $form);
        $templates = $this->_getDefaultFormTemplates();
        
        $this->_copyTemplates($templates[0], $templates[1]);
        
        $this->_writeln("Form is created");
    }
    
    public function deleteForm($module, $name, $form) {
        $this->_prepareObject($module, $name, $form);
        $templates = $this->_getDefaultFormTemplates();
        
        $this->_deleteTemplates($templates[0], $templates[1]);
        
        
        $this->_writeln("Form is deleted");
    }
    
    
    public function createGrid($module, $name, $grid) {
        $this->_prepareObject($module, $name, $grid);
        $templates = $this->_getGridTemplates();
        
        $this->_copyTemplates($templates[0], $templates[1]);
        
        $this->_writeln("Grid is created");
    }
    
    public function deleteGrid($module, $name, $grid) {
        $this->_prepareObject($module, $name, $grid);
        $templates = $this->_getGridTemplates();
        
        $this->_deleteTemplates($templates[0], $templates[1]);
        $this->_writeln("Grid is deleted");
    }
}
