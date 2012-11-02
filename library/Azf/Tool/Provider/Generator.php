<?php

/**
 * Description of store
 *
 * @author antun
 */
class Azf_Tool_Provider_Generator extends Azf_Tool_Provider_AbstractPlugin{

    

    public function getComponentName() {
        return "";
    }

    protected function _prepareObject($module, $name, $resource = "") {
        parent::_prepareObject($module, $name, $resource);
        $this->_baseJsPath = $this->_getJsGeneratorBasePath($module, $name);
        $this->_baseJsTestPath = $this->_getTestJsGeneratorBasePath($module, $name);
    }
    
    
    
    
    protected function _getGeneratorTemplates() {
        return array(
            $this->_baseJsPath => array(
                array('Generator.js',$this->_ucName."Generator.js"),
                array('DefaultTemplate.html',"templates/".$this->_ucName."Generator.html")
            ),
            $this->_baseJsTestPath => array(
                array('DefaultGeneratorInvokerTest.html',$this->_ucName."Generator.html")
            )
        );
    }
    
    protected function _getDirectoryLayout() {
        return array(
            $this->_baseJsPath=>array(
                'templates'
            ),
            $this->_baseJsTestPath =>array()
        );
    }
    
  
    
    
    public function create($module, $name) {
        $this->_prepareObject($module, $name);
        $this->_createLayoutV2($this->_getDirectoryLayout());
        
        $this->_copyTemplatesV2($this->_getGeneratorTemplates());
        
        $this->_writeln("Generator is created");
    }
    
    public function delete($module, $name) {
        $this->_prepareObject($module, $name);
        
        $this->_deleteTemplatesV2($this->_getGeneratorTemplates());
        $this->_deleteLayoutV2($this->_getDirectoryLayout());
        
        $this->_writeln("Generator is deleted");
    }
    
    
    
}
