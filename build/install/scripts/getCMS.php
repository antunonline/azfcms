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
    
    $fp = fopen("https://nodeload.github.com/antunonline/azfcms/zipball/Deployable","rb");
    $dst = fopen("cms.zip",'w');
    while($data = fread($fp, 4096)){
        fwrite($dst,$data);
    }
    
    $log->writeln("CMS Downloaded");
    
};
?>
