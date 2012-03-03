<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_PHPUnit_Db_Utils {

    /**
     * @return Zend_Db_Adapter_Abstract 
     */
    public static function getZendConnection() {
        return Azf_PHPUnit_Db_ConnectionFactory::getConnection();
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection 
     */
    public static function getPHPUNitConnection() {
        return Azf_PHPUnit_Db_ConnectionFactory::getPHPUnitConnection();
    }

    /**
     * Execute SQL connection
     * @param string $sql 
     * @return void
     */
    public static function execSql($sql) {
        self::getZendConnection()->query($sql);
    }

    /**
     *
     * @param string $tableName
     * @return boolean 
     */
    public static function tableExist($tableName) {
        $dbConfig = Azf_PHPUnit_Configuration::get("db");
        $schema = $dbConfig['dbname'];

        $SQL = <<<SQL
SELECT TABLE_NAME FROM `information_schema`.TABLES WHERE
    TABLE_NAME = ? AND TABLE_SCHEMA = ?;
SQL;
        if (count(Azf_PHPUnit_Db_Utils::getZendConnection()->fetchAll($SQL, array($tableName, $schema)))) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    /**
     * 
     * @param string $tableName 
     * @return void
     */
    public static function createTable($tableName){
        $sql = file_get_contents("../docs/SQL/".ucfirst($tableName).".sql");
        self::execSql($sql);
    }
    
    
    /**
     *
     * @param string $tableName 
     * @return void
     */
    public static function truncateTable($tableName){
        $SQL = "TRUNCATE TABLE $tableName";
        self::getZendConnection()->query($SQL);
    }
    
    
    /**
     *
     * @param string $xmlSourcePath 
     */
    public static function populateTable($xmlSourcePath){
        $xmlDataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlSourcePath);
        PHPUnit_Extensions_Database_Operation_Factory::INSERT()
                ->execute(self::getPHPUNitConnection(), $xmlDataSet);
    }

}
