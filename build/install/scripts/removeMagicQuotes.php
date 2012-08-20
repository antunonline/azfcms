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
    
    $fnc = 
<<<GPC
  if (get_magic_quotes_gpc()) {
    \$process = array(&\$_GET, &\$_POST, &\$_COOKIE, &\$_REQUEST);
    while (list(\$key, \$val) = each(\$process)) {
        foreach (\$val as \$k => \$v) {
            unset(\$process[\$key][\$k]);
            if (is_array(\$v)) {
                \$process[\$key][stripslashes(\$k)] = \$v;
                \$process[] = &\$process[\$key][stripslashes(\$k)];
            } else {
                \$process[\$key][stripslashes(\$k)] = stripslashes(\$v);
            }
        }
    }
    unset(\$process);  
    }
GPC;
    
    $injectFnc = function($file,$fnc){
        $fileLines = file($file);
        array_splice($fileLines, 1, 0,$fnc);
        file_put_contents($file, join("\n",$fileLines));
    };
    
    $injectFnc("index.php",$fnc);
    $injectFnc("json-lang.php",$fnc);
    $injectFnc("json-rpc.php",$fnc);
    $injectFnc("json-rest.php",$fnc);
    
    $log->writeln("Magic quotes removal function attached");
};
?>
