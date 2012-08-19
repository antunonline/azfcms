<?php

$xml = simplexml_load_file(__DIR__."/Privileges.xml");
$sqlFp = fopen(__DIR__.'/Privileges.sql','w');

$sqlInsertQuery = "INSERT IGNORE INTO Acl (resource,description) VALUES \n";
$sqlValueChunks = array();
foreach($xml->resources->resource as $resource){
    $sqlValueChunks[] = "('$resource->name','$resource->description')\n";
}

$sqlInsertQuery.=join(",\n",$sqlValueChunks).";\n";

fwrite($sqlFp, $sqlInsertQuery);
fclose($sqlFp);


