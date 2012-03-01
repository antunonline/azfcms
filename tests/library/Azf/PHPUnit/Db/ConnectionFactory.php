<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConnectionFactory
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_PHPUnit_Db_ConnectionFactory {

    protected static $adapter;

    protected static function _initAdapter() {
        if (self::$adapter)
            return;

        $initConfig = new Zend_Config_Ini("configuration.ini");
        
        $adapter = Zend_Db::factory("pdo_mysql",$initConfig->get("db"));
        self::$adapter = $adapter;
    }

    public static function initDefaultDbTableAdapter() {
        if (!self::$adapter)
            self::_initAdapter();

        Zend_Db_Table_Abstract::setDefaultAdapter(self::$adapter);
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public static function getConnection() {
        self::_initAdapter();

        return self::$adapter;
    }

    /**
     *
     * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection 
     */
    public static function getPHPUnitConnection() {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection(self::getConnection()->getConnection());
    }

}
