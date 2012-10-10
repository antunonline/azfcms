<?php

require_once __DIR__."/AbstractBuilder.php";

/**
 * Description of TemplateBuilder
 *
 * @author antun
 */
class Azf_Tool_DirectoryBuilder extends Azf_Tool_AbstractBuilder{
    protected $_basePath = ".";
    
    public function getBasePath() {
        return $this->_basePath;
    }

    public function setBasePath($basePath) {
        $this->_basePath = $basePath;
    }
    
    public function createBasepath($path) {
        if($this->fullPathExists($path)==false){
            mkdir($path,0777,true);
            $this->log($path);
        }
        
        $this->setBasePath($path);
    }
    
    

    public function createLayout($directories) {
        $basePath = $this->getBasePath();
        $newPath =null;
        foreach($directories as $dirName){
            $newPath = $basePath."/$dirName";
            $this->log("A ".$newPath);
            $this->createFullPathDirectory($newPath);
        }
    }
    
    
    public function createFile($filename,$source) {
        $this->createFullPathFile($this->getBasePath()."/$filename", $source);
    }
    
    public function createDirectory($name) {
        $this->createFullPathDirectory($this->getBasePath()."/$name");
    }
    
    public function destroyRecursive() {
        $basePath = $this->getBasePath();
        $this->destroyRecursiveFullPath($basePath);
    }
    
    public function delete($name) {
        $path= $this->getBasePath()."/$name";
        if($this->fullPathExists($path)==false){
            return;
        }
        $this->deleteFullPath($path);
    }
    
    public function basePathExists() {
        return $this->fullPathExists($this->getBasePath());
    }
    
    
    public function fullPathExists($path) {
        return file_exists($path);
    }
    
    public function destroyRecursiveFullPath($path) {
        if($this->fullPathExists($path)==false){
            return;
        }
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($dirIterator,  RecursiveIteratorIterator::CHILD_FIRST);
        $item=null;
        
        while($iterator->valid()){
            $item = $iterator->current();
            /* @var $item DirectoryIterator */
            $this->log("D ".$item->getPathname());
            
            if($item->isDir()){
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
            
            $iterator->next();
        }
        @rmdir($path);
    }
    
    public function deleteFullPath($path) {
        if(file_exists($path)){
            if(is_dir($path)){
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        $this->log("D ".$path);
    }
    
    
    public function createFullPathDirectory($path) {
        if($this->fullPathExists($path)){
            $this->log("Directory $path already exists");
            return;
        }
        
        mkdir($path,0777,true);
        $this->log("A ".$path);
    }
    
    
    public function createFullPathFile($path,$source) {
        if($this->fullPathExists($path)){
            $this->log("File $path already exists");
            return;
        }
        file_put_contents($path, $source);
        $this->log("A ".$path);
    }
}
