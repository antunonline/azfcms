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
    
    $fp = fopen("http://framework.zend.com/releases/ZendFramework-1.11.12/ZendFramework-1.11.12.zip","rb");
    $dst = fopen("zf.zip",'w');
    while($data = fread($fp, 4096)){
        fwrite($dst,$data);
    }
    
    $log->writeln("Zend Framework Downloaded");
    
};
?>
