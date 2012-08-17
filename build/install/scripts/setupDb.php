<?php 
if(defined("DEFINE_BUILD_DEPS")){
    /* @var $this InstallScriptBuilder */
    return;
}
?><?php


/**
 * Install DB Schema
 */
$install[] = function($log,$dbHost,$dbUser,$dbPassword,$dbName,$dbDDL,$dbDML){
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName",$dbUser,$dbPassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    
    $stmt = $pdo->prepare($dbDDL);
    $stmt->execute();
    do{
        $stmt->fetchAll();
        if($stmt->errorCode()){
            print_r($stmt->errorInfo());
        }
    } while($stmt->nextRowset());
    
    
    $stmt = $pdo->prepare($dbDML);
    $stmt->execute();
    do{
        $stmt->fetchAll();
        if($stmt->errorCode()){
            print_r($stmt->errorInfo());
        }
    } while($stmt->nextRowset());
    
};
?>
