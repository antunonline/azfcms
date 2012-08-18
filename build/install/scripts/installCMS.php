<?php 
if(defined("DEFINE_BUILD_DEPS")){
    /* @var $this InstallScriptBuilder */
    return;
}
?><?php

/**
 * Load Zend Framework 
 */
$install[] = function(InstallWorkerLog $log){
    
    
    $mv2Private = function($from,$to){
        $privateBase = "../";
        $publicBase = "./";
        
        $from = $publicBase.$from;
        $to = $privateBase.$to;
        
        if(file_exists($to)){
            if(is_file($to)){
                unlink($to);
            } else {
                recursiveDirectoryDelete($to);
            }
        }
        
        rename($from,$to);
    };
    $mv2Public = function($from,$to){
        $privateBase = "../";
        $publicBase = "./";
        
        $from = $publicBase.$from;
        $to = $publicBase.$to;
        
        if(file_exists($to)){
            if(is_file($to)){
                unlink($to);
            } else {
                recursiveDirectoryDelete($to);
            }
        }
        
        rename($from,$to);
    };
    
    
    $mv2Private("cms/application","application");
    $mv2Private("cms/library","library");
    $mv2Private("zf/library/Zend","library/Zend");
    $mv2Public("cms/public/css","css");
    $mv2Public("cms/public/js","js");
    $mv2Public("cms/public/templates","templates");
    $mv2Public("cms/public/.htaccess",".htaccess");
    $mv2Public("cms/public/json-lang.php","json-lang.php");
    $mv2Public("cms/public/index.php","index.php");
    $mv2Public("cms/public/json-rest.php","json-rest.php");
    $mv2Public("cms/public/json-rpc.php","json-rpc.php");
    $mv2Public("dojo/dojo","js/lib/dojo");
    $mv2Public("dojo/dijit","js/lib/dijit");
    $mv2Public("dojo/dojox","js/lib/dojox");
    
    
    
    
            
    $log->writeln("Done moving files");
};
?>
