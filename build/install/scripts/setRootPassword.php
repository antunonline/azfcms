<?php

if (defined("DEFINE_BUILD_DEPS")) {
    /* @var $this InstallScriptBuilder */
    
    $this->addVarFromOpt("rootPassword");
    $password = $this->getVar("rootPassword");
    $passwordHash = sha1($password);
    $this->addVar("rootPassword", $passwordHash);
    
    
    return;
}
?><?php

/**
 * Install DB Schema
 */
$install[] = function(InstallWorkerLog $log, $dbHost, $dbUser, $dbPassword, $dbName, $rootPassword) {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            $updateSQL = "UPDATE User SET password = ?";
            $stmt = $pdo->prepare($updateSQL);
            $stmt->bindValue(1, $rootPassword);
            $stmt->execute();
            
            $log->writeln("Root password changed");
        };
?>
