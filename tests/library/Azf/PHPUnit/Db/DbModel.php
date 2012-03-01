<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBModel
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_PHPUnit_Db_DbModel {

    public static function getTreeSum() {
        $connection0 = Azf_PHPUnit_Db_ConnectionFactory::getConnection();
        return $connection0->fetchOne("SELECT sum(l)+sum(r) as `sum` FROM Tree WHERE tid = 1");
    }


    public static function clean() {
        Azf_PHPUnit_Db_ConnectionFactory::initDefaultDbTableAdapter();
        $connection = Azf_PHPUnit_Db_ConnectionFactory::getConnection();

        $connection->query("TRUNCATE TABLE Tree;");
    }

    public static function reset() {
        self::clean();
    }

    public static function populate($xmlFilePath) {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlFilePath);
        $phpUnitConnection = Azf_PHPUnit_Db_ConnectionFactory::getPHPUnitConnection();
        
        PHPUnit_Extensions_Database_Operation_Factory::INSERT()->execute($phpUnitConnection, $dataSet);
    }

}

