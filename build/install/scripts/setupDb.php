<?php

if (defined("DEFINE_BUILD_DEPS")) {
    /* @var $this InstallScriptBuilder */
    return;
}
?><?php

/**
 * Install DB Schema
 */
$install[] = function(InstallWorkerLog $log, $dbHost, $dbUser, $dbPassword, $dbName, $dbDDL, $dbDML) {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

            $stmt = $pdo->query($dbDDL);
            do{
                $stmt->fetchAll();
            }while($stmt->nextRowset());
            
            $stmt = $pdo->query($dbDML);
            do{
                $stmt->fetchAll();
            }while($stmt->nextRowset());
            
            $log->writeln("DB Schema installed");
        };
?>
