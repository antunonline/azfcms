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
class PHPUnit_TreeModel_DBModel {

    public static function getTreeSum() {
        $connection0 = PHPUnit_TreeModel_ConnectionFactory::getConnection();
        return $connection0->fetchOne("SELECT sum(l)+sum(r) as `sum` FROM Tree WHERE tid = 1");
    }


    public static function clean() {
        PHPUnit_TreeModel_ConnectionFactory::initDefaultDbTableAdapter();
        $connection = PHPUnit_TreeModel_ConnectionFactory::getConnection();

        $connection->query("TRUNCATE TABLE Tree");
    }

    public static function reset() {
        self::clean();
    }

    public static function populate($xmlFilePath) {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlFilePath);
        $phpUnitConnection = PHPUnit_TreeModel_ConnectionFactory::getPHPUnitConnection();
        
        PHPUnit_Extensions_Database_Operation_Factory::INSERT()->execute($phpUnitConnection, $dataSet);
    }

}

