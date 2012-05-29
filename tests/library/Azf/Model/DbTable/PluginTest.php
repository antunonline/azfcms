<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin
 *
 * @author antun
 */
class Azf_Model_DbTable_PluginTest extends PHPUnit_Extensions_Database_TestCase{
    
    public function assertDbTableRowCount($tableName, $expected, $message = '') {
        $constraint = new PHPUnit_Extensions_Database_Constraint_TableRowCount($tableName, $expected);
        $actual = $this->getConnection()->getRowCount($tableName);

        self::assertThat($actual, $constraint, $message);
    }
    
    public function assertTableContains(array $expectedRow, PHPUnit_Extensions_Database_DataSet_ITable $table, $message = '') {
        // Preload table
        $table->getTableMetaData();
        parent::assertTableContains($expectedRow, $table, $message);
    }
    //put your code here
    protected function getConnection() {
        return Azf_PHPUnit_Db_Utils::getPHPUNitConnection();
    }
    protected function getDataSet() {
        return $this->createFlatXMLDataSet(__DIR__."/resources/Plugin.xml");
    }
    
    public function getInstance(){
        return new Azf_Model_DbTable_Plugin(array('db'=>  Azf_PHPUnit_Db_ConnectionFactory::getConnection()));
    }
    
    public function testInsertPlugin() {
        $instance = $this->getInstance();
        $instance->insertPlugin("name9", "description9", "type9", "right", array());
        $table = $this->getConnection()->createQueryTable("Plugin", "select * from Plugin;");
        $this->assertDbTableRowCount("Plugin", 9);
        $this->assertTableContains(array(
            'id'=>9,
            'name'=>'name9',
            'description'=>'description9',
            'type'=>'type9',
            'region'=>'right',
            'params'=>'[]'
        ), $table);
    }
   
    public function testDeleteById() {
        $instance = $this->getInstance();
        $instance->deleteById(8);
        $xmlPluginTable = $this->getDataSet()->getTable("Plugin");
        (function(){});
        $this->assertDbTableRowCount("Plugin", 7);
        // Create new table without 8'th row
        for($i = 0; $i < 7; $i++) {
            $rows[] = $xmlPluginTable->getRow($i);
        }
        $filteredDataSet = new Azf_PHPUnit_Db_ArrayDataSet(array(
            'Plugin'=>$rows
        ));
        
        $expected = $filteredDataSet->getTable("Plugin");
        $actual = $this->getConnection()->createQueryTable("Plugin", "SELECT * FROM Plugin");
        
        $this->assertTablesEqual($expected, $actual);
    }
    
    public function testGetPluginParams() {
        $expected = array('key'=>'value8','nested'=>array('key'=>'value'));
        $actual = $this->getInstance()->getPluginParams(8);
        
        $this->assertEquals($expected,$actual);
    }
    
    public function testFindAllByNavigationid(){
        $expected = array(
            array(
                'id'=>'1',
            'type'=>'type1',
            'name'=>'name1',
            'description'=>'description1',
            'region'=>'left',
            'params'=>array(),
            'weight'=>'100'
            )
        );
        $actual = $this->getInstance()->findAllByNavigationid(1);
        
        $this->assertEquals($expected,$actual);
    }
    
    public function testFindAllByNavigationidCase2(){
        $expected = array(
            array(
                'id'=>'4',
            'type'=>'type4',
            'name'=>'name4',
            'description'=>'description4',
            'region'=>'left',
            'params'=>array(),
            'weight'=>'90'
            ),
            array(
                'id'=>'2',
            'type'=>'type2',
            'name'=>'name2',
            'description'=>'description2',
            'region'=>'left',
            'params'=>array(),
            'weight'=>'200'
            ),
            array(
                'id'=>'5',
            'type'=>'type5',
            'name'=>'name5',
            'description'=>'description5',
            'region'=>'right',
            'params'=>array(),
            'weight'=>'500'
            ),
            array(
                'id'=>'7',
            'type'=>'type7',
            'name'=>'name7',
            'description'=>'description7',
            'region'=>'right',
            'params'=>array('key'=>'value7'),
            'weight'=>'700'
            ),
            array(
                'id'=>'8',
            'type'=>'type8',
            'name'=>'name8',
            'description'=>'description8',
            'region'=>'right',
            'params'=>array('key'=>'value8','nested'=>array('key'=>'value')),
            'weight'=>'800'
            )
        );
        $actual = $this->getInstance()->findAllByNavigationid(2);
        
        $this->assertEquals($expected,$actual);
    }
}

