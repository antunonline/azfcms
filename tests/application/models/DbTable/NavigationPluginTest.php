<?php
require './../application/models/DbTable/NavigationPlugin.php';
class Application_Model_DbTable_NavigationPluginTest extends PHPUnit_Extensions_Database_TestCase {
    
    
    public function assertTableContains(array $expectedRow, PHPUnit_Extensions_Database_DataSet_ITable $table, $message = '') {
        // Load data
        $table->getTableMetaData();
        parent::assertTableContains($expectedRow, $table, $message);
    }
    
    public function assertTableNotContains(array $expectedRow, PHPUnit_Extensions_Database_DataSet_ITable $table, $message = '') {
        // Load data
        $table->getTableMetaData();
        self::assertThat($table->assertContainsRow($expectedRow), self::isFalse(), $message);
    }
    
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        Azf_PHPUnit_Db_SchemaUtils::buildSchema();
    }

    protected function getConnection() {
        return Azf_PHPUnit_Db_ConnectionFactory::getPHPUnitConnection();
    }

    protected function getDataSet() {
        return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(__DIR__.'/resources/NavigationPluginTest.xml');
    }
    
    /**
     *
     * @return \Application_Model_DbTable_Plugin 
     */
    public function getModel(){
        return new Application_Model_DbTable_Plugin(array('db'=>  Azf_PHPUnit_Db_ConnectionFactory::getConnection()));
    }
    
    
    /**
     * 
     */
    public function testBind(){
        $this->getModel()->bind(2,2,220);
        $table = $this->getConnection()->createQueryTable('', "SELECT * FROM NavigationPlugin");
        $this->assertTableContains(array('id'=>9,'navigationId'=>2,'pluginId'=>2,'weight'=>220), $table);
        
        $this->assertEquals(9, $table->getRowCount());
    }
    
    public function testUnbind(){
        $this->getModel()->unbind(1, 2);
        $table = $this->getConnection()->createQueryTable("", "select * from NavigationPlugin");
        $this->assertTableNotContains(array(), $table);
        
        $this->assertEquals(7, $table->getRowCount());
    }
    
    
    public function testFindAllByNavigationAndRegion(){
        $actual = $this->getModel()->findAllByNavigationAndRegion(1, "left");
        // id, navigationId , pluginId, weight, displayName, displayDescription, `type`, 0 as disabled
        $expected = array(
            array('id'=>"3", 'navigationId'=>"1", 'pluginId'=>"3",'weight'=>"90",
                'displayName'=>'On3','displayDescription'=>'Desc One',
                'type'=>'type3','disabled'=>"0"),
            array('id'=>"1", 'navigationId'=>"1", 'pluginId'=>"1",'weight'=>"100",
                'displayName'=>'On1','displayDescription'=>'Desc One',
                'type'=>'type1','disabled'=>"0"),
            array('id'=>"2", 'navigationId'=>"1", 'pluginId'=>"2",'weight'=>"110",
                'displayName'=>'On2','displayDescription'=>'Desc One',
                'type'=>'type2','disabled'=>"0"),
            array('id'=>null, 'navigationId'=>null, 'pluginId'=>"4",'weight'=>"0",
                'displayName'=>'On4','displayDescription'=>'Desc One',
                'type'=>'type4','disabled'=>"1"),
        );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testFindAllByNavigation1AndRegionRightRegion(){
        $actual = $this->getModel()->findAllByNavigationAndRegion(1, "right");
        // id, navigationId , pluginId, weight, displayName, displayDescription, `type`, 0 as disabled
        $expected = array(
            array('id'=>null, 'navigationId'=>null, 'pluginId'=>"5",'weight'=>"0",
                'displayName'=>'On5','displayDescription'=>'Desc One',
                'type'=>'type5','disabled'=>"1"),
            array('id'=>null, 'navigationId'=>null, 'pluginId'=>"6",'weight'=>"0",
                'displayName'=>'On6','displayDescription'=>'Desc One',
                'type'=>'type6','disabled'=>"1")
        );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testUpdateWeight(){
        $this->getModel()->updateWeight(4, "110");
        $table = $this->getConnection()->createQueryTable("", "SELECT * FROM NavigationPlugin");
        $this->assertTableContains(array(
            'id'=>4,'pluginId'=>'1','navigationId'=>'2','weight'=>"110"
        ), $table);
    }
    
    public function testUpdateWeightIntegrityCheck(){
        $this->getModel()->updateWeight(4, "100");
        $actual = $this->getConnection()->createQueryTable("NavigationPlugin", "SELECT * FROM NavigationPlugin");
        $expected = $this->getDataSet()->getTable("NavigationPlugin");
        
        $this->assertTrue($expected->matches($actual));
        
    }
    
}
