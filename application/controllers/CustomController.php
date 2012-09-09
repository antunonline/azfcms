<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomController
 *
 * @author antun
 */
class CustomController extends Azf_Controller_Action{
    
    
    public function installpageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $navigation = $this->getNavigation();
        $id = $this->getNavigationId();
        
        
        $navigation->setStaticParam($id, "module", "default");
        $navigation->setStaticParam($id, "controller", "custom");
        $navigation->setStaticParam($id, "action", "render");
    }
    
    
    protected $_validValueKeys = array("module","controller","action");
    
    /**
     * Alter mapping from predefiend MVC to value of our choice.
     * 
     * @param string $key
     * @param string $value
     */
    public function setValue($key, $value) {
        if(in_array($key,$this->_validValueKeys)){
            $navigation = $this->getNavigation();
            $navigation->setStaticParam($this->getNavigationId(), $key, $value);
        }
    }
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getValue($key) {
        if(in_array($key,$this->_validValueKeys)){
            return $this->getNavigation()
                    ->getStaticParam($this->getNavigationId(), $key);
        } else {
            return false;
        }
    }
    
    
    /**
     * 
     * @return array
     */
    public function getValues() {
        $values = $this->getNavigation()->getStaticParams($this->getNavigationId());
        return array_intersect_key($values, array_flip($this->_validValueKeys));
    }
    
    public function setValues($values) {
        $validValues= array_intersect_key($values, array_flip($this->_validValueKeys));
        
        $navigation = $this->getNavigation();
        $id = $this->getNavigationId();
        foreach($validValues as $key=>$value){
            $navigation->setStaticParam($id, $key, $value);
        }
        
        return true;
    }

    public function renderAction() {
        
    }

    public function uninstallpageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam("id");
        $navigation = $this->getNavigation();
        
        $navigation->deleteStaticParam($id, 'module');
        $navigation->deleteStaticParam($id, 'controller');
        $navigation->deleteStaticParam($id, 'action');
    }
}

