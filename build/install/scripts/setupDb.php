<?php

if (defined("DEFINE_BUILD_DEPS")) {
    /* @var $this InstallScriptBuilder */
    
    $this->addVarFromFile("dbDDL", "../resources/DDL.sql");
    $this->addVarFromFile("dbDML", "../resources/DML.sql");
    $this->addVarFromFile("dbPrivileges", "../resources/privileges.sql");
    
    $this->addVarFromOpt("dbHost");
    $this->addVarFromOpt("dbUser");
    $this->addVarFromOpt("dbPassword");
    $this->addVarFromOpt("dbName");
    
    return;
}
?><?php

/**
 * Install DB Schema
 */
$install[] = function(InstallWorkerLog $log, $dbHost, $dbUser, $dbPassword, $dbName, $dbDDL, $dbDML,$dbPrivileges) {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            $stmt = $pdo->query($dbDDL);
            do  {
                $stmt->closeCursor();
            } while($stmt->nextRowset());
            
            $stmt = $pdo->query($dbDML);
            do  {
                $stmt->closeCursor();
            } while($stmt->nextRowset());
            
            $stmt = $pdo->query($dbPrivileges);
            do  {
                $stmt->closeCursor();
            } while($stmt->nextRowset());
            
            $log->writeln("DB Schema installed");
        };
?>
