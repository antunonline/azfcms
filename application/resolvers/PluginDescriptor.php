<?php

class Application_Resolver_PluginDescriptor extends Azf_Service_Lang_Resolver {
    
    

        
    public function initialize() {
        parent::initialize();
    }

    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
    
    
    /**
     *
     * @return array
     */
    public function getContentPluginsMethod(){
        $pd = new Azf_Plugin_Descriptor();
        
        $plugins = $pd->getContentPlugins();
        
        return array(
            'data'=>$plugins,
            'total'=>  sizeof($plugins)
        );
    }
    
    
    /**
     *
     * @return array
     */
    public function getExtensionPluginsMethod(){
        $pd = new Azf_Plugin_Descriptor();
        $plugins = $pd->getExtensionPlugins();
        
        return array(
            'data'=>$plugins,
            'total'=>sizeof($plugins)
        );
    }
    

}
