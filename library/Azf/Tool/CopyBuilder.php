<?php

require_once __DIR__.'/AbstractBuilder.php';

/**
 * Description of TemplateBuilder
 *
 * @author antun
 */
class Azf_Tool_CopyBuilder extends Azf_Tool_AbstractBuilder{
    protected $_baseSrcPath;
    
    protected $_baseDstPath;
    
    protected $_baseTemplatePath;
    
    
    public function getBaseSrcPath() {
        return $this->_baseSrcPath;
    }

    public function setBaseSrcPath($baseSrcPath) {
        $this->_baseSrcPath = $baseSrcPath;
    }

    public function getBaseDstPath() {
        return $this->_baseDstPath;
    }

    public function setBaseDstPath($baseDstPath) {
        $this->_baseDstPath = $baseDstPath;
    }
    
    public function getBaseTemplatePath() {
        return $this->_baseTemplatePath;
    }

    public function setBaseTemplatePath($baseTemplatePath) {
        $this->_baseTemplatePath = $baseTemplatePath;
    }

    
    
    public function copy($srcFile,$dstFile) {
        $src = $this->getBaseSrcPath()."/$srcFile";
        $dst = $this->getBaseDstPath()."/$dstFile";
        
        if(file_exists($dst)){
            $this->log("File $dst already exists");
            return;
        }
        
        $this->log("A ".$dst);
        return copy($src,$dst);
    }
    
    
    public function copyTemplate($srcTemplate,$dstFile,$templateArguments) {
        $src = $this->getBaseTemplatePath()."/$srcTemplate";
        $dst = $this->getBaseDstPath()."/$dstFile";
        
        if(file_exists($dst)){
            $this->log("File $dst already exists");
            return;
        }
        
        extract($templateArguments);
        ob_start();
        include($src);
        $source = ob_get_clean();
        file_put_contents($dst, $source);
        
        $this->log("A ".$dst);
        
        return true;
    }
    
    
    


}
