<?php

$xml = simplexml_load_file(__DIR__."/Privileges.xml");
$sqlFp = fopen(__DIR__.'/Privileges.sql','w');

$sqlInsertQuery = "INSERT INTO Acl (name,description) VALUES ";
$sqlValueChunks = array();
foreach($xml->privileges->privilege as $privilege){
    $sqlValueChunks[] = "('$privilege->name','$privilege->description')";
}

$sqlInsertQuery.=join(",\n",$sqlValueChunks).";\n";

fwrite($sqlFp, $sqlInsertQuery);
fclose($sqlFp);

