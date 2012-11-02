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
        
        $this->_copyTemplatesV2($this->_getStoreTemplates());
        
        $this->_writeln("Store is created");
    }
    
    public function delete($module, $name) {
        $this->_prepareObject($module, $name);
        
        $this->_deleteTemplatesV2($this->_getStoreTemplates());
        
        $this->_writeln("Store is deleted");
    }
    
    
    
}
