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
     * @return array
     */
    public function getConfigurationActionFileList() {
        $configurationActionDefinitionList = array();
        $configurationActionDirectoryIterator = $this->getConfigurationActionDirectoryIterator();
        while($configurationActionDirectoryIterator->valid()){
            $path = $configurationActionDirectoryIterator->getPathname();
            
            if($configurationActionDirectoryIterator->isFile() && ("js"==  pathinfo($path,PATHINFO_EXTENSION))){
                $configurationActionDefinitionList[] =$path;
            }
            
            $configurationActionDirectoryIterator->next();
        }
        
        sort($configurationActionDefinitionList,SORT_NUMERIC);
        return $configurationActionDefinitionList;
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
        $configurationActionDefinitionList = array();
        
        $fileList= $this->getConfigurationActionFileList();
        for($i=0,$len=sizeof($fileList);$i<$len;$i++){
            $configurationActionDefinitionList[] = $this->getConfigurationActionDefinition($fileList[$i]);
        }
        
        return $configurationActionDefinitionList;
    }
}
