<?php 
if(defined("DEFINE_BUILD_DEPS")){
    /* @var $this InstallScriptBuilder */
    return;
}
?><?php 

$install[] = function(InstallWorkerLog $log){
    
    $fp  = zip_open(realpath("cms.zip"));
    if(!is_resource($fp)){
        $log->writeln(new Exception("Could not open zend framework distribution archive"));
    }
    
    
    while($entry = zip_read($fp)){
        
        $name = zip_entry_name($entry);
        $size = zip_entry_filesize($entry);
        if(!isset($rootDirName)){
            $rootDirName = $name;
        }
        
        if($size==0){
            if(file_exists($name)){
                continue;;
            }
            mkdir($name);
        }  else {
            $dstFp = fopen($name,'w');
            while($data = zip_entry_read($entry,2048)){
                fwrite($dstFp, $data);
            }
            fclose($dstFp);
        }
    }
    
    recursiveDirectoryDelete("cms");
    rename($rootDirName,"cms");
    
    $log->writeln("Done extracting CMS");
    
};
?>