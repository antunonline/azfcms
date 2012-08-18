<?php 
if(defined("DEFINE_BUILD_DEPS")){
    /* @var $this InstallScriptBuilder */
    return;
}
?><?php


/**
 *Load Dojo toolkit 
 */
$install[] = function($log){
    $fp = fopen("http://download.dojotoolkit.org/release-1.8.0/dojo-release-1.8.0.zip","rb");
    $dst = fopen("dojo.zip",'w');
    while($data = fread($fp, 4096)){
        fwrite($dst,$data);
    }
    
    $log->writeln("Dojo Downloaded");
};
?>
