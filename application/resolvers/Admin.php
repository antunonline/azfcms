<?php

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Admin extends Azf_Service_Lang_Resolver {
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
    
    
    /**
     * 
     * @return \DirectoryIterator
     */
    public function getConfigurationActionDirectoryIterator() {
        return new DirectoryIterator(APPLICATION_PATH."/../public/js/lib/azfcms/bootstrap/configuration");
    }
    
    
    /**
     * 
     * @param string $path
     * @return string
     */
    public function getConfigurationActionDefinition($path) {
        return file_get_contents($path);
    }
    
    
    /**
     * @return array
     */
    public function getConfigurationActionDefinitionsMethod(){
        $configurationActionDirectoryIterator = $this->getConfigurationActionDirectoryIterator();
        $configurationActionDefinitionList = array();
        
        while($configurationActionDirectoryIterator->valid()){
            $path = $configurationActionDirectoryIterator->getPathname();
            
            if($configurationActionDirectoryIterator->isFile() && ("js"==  pathinfo($path,PATHINFO_EXTENSION))){
                $configurationActionDefinitionList[] = $this->getConfigurationActionDefinition($path);
            }
            
            $configurationActionDirectoryIterator->next();
        }
        
        return $configurationActionDefinitionList;
    }
}
