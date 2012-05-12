<?php

class Application_Resolver_PluginDescriptor extends Azf_Service_Lang_Resolver {
    
    

        
    public function initialize() {
        parent::initialize();
    }

    protected function _execute(array $namespaces, array $parameters) {
        if(sizeof($namespaces)!=1){
            return null;
        }
        
        $method = array_pop($namespaces);
        
        if(method_exists($this, $method)){
            return call_user_method_array($method, $this, $parameters);
        } else {
            return null;
        }
    }
    
    
    /**
     *
     * @return array
     */
    public function getContentPlugins(){
        $pd = new Azf_Plugin_Descriptor();
        return $pd->getContentPlugins();
    }
    
    
    /**
     *
     * @return array
     */
    public function getExtensionPlugins(){
        $pd = new Azf_Plugin_Descriptor();
        return $pd->getExtensionPlugins();
    }
    
    
    protected function isAllowed($namespaces, $parameters) {
        // TODO fix me
        return true;
    }

}
