<?php

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Admin extends Azf_Service_Lang_Resolver {
    protected function isAllowed($namespaces, $parameters) {
        return Azf_Acl::hasAccess("resource.admin.rw");
    }
    
    
    /**
     * 
     * @return \DirectoryIterator
     */
    public function getConfigurationActionDirectoryIterator() {
        return new DirectoryIterator(getcwd()."/js/lib/azfcms/bootstrap/configuration");
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
        
        sort($configurationActionDefinitionList,SORT_STRING);
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
    
    protected function _getJsModulePaths() {
        $dirIterator = new DirectoryIterator(APPLICATION_PATH."/../public/js/lib/azfcms/module");
        $modulePaths = array();
        
        foreach($dirIterator as $file){
            if($file->isDir() && $file->isDot()==false){
                $modulePaths[] = $file->getPathname();
            }
        }
        
        return $modulePaths;
    }
    
    protected function _getJsGeneratorPaths($modulePaths) {
        $servicePaths = array();
        foreach($modulePaths as $modulePath){
            $dirIterator= new DirectoryIterator($modulePath."/generator");
            foreach($dirIterator as $file){
                if($file->isDir()&&$file->isDot()==false){
                    $servicePaths[] = $file->getRealPath();
                }
            }
        }
        
        return $servicePaths;
        
    }
    
    protected function _buildAmdRequirePaths($servicePaths) {
        $normModulePath = realpath(APPLICATION_PATH."/../public/js/lib")."/";
        
        foreach($servicePaths as $key=>$path){
            $basename = basename($path);
            $servicePaths[$key] = str_replace($normModulePath, "", $path). "/". ucfirst($basename)."Generator";
        }
        
        return $servicePaths;
    }
    
    public function getGeneratorAmdListMethod() {
        $modulePaths = $this->_getJsModulePaths();
        $servicePaths = $this->_getJsGeneratorPaths($modulePaths);
        return $this->_buildAmdRequirePaths($servicePaths);
    }
}
