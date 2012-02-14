<?php

class User_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    public function _initRest(){
        if(strpos($this->getEnvironment(),"json-rest")===0){
            $this->_initRestLoaderResource();
        }
    }
    
    private function _initRestLoaderResource(){
        $this->getResourceLoader()->addResourceType("rests", "rests", "Rest");
    }

    public function run() {
        return true;
    }
}
