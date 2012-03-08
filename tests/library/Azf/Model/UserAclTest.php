<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserAclTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_UserAclTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        Azf_PHPUnit_Db_ConnectionFactory::initDefaultDbTableAdapter();
    }
    
    /**
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getSelfMock(array $methods){
        $mock = $this->getMock("Azf_Model_UserAcl",$methods);
        return $mock;
    }
    
    /**
     * @test
     */
    public function testBind(){
        $mock = $this->getSelfMock(array("insert"));
        $mock->expects($this->once())->method("insert")
                ->with(array('userId'=>1,'aclGroupId'=>2))
                ->will($this->returnValue(1));
        
        $actual = $mock->bind(1,2);
        $this->assertEquals(1,$actual);
    }
    
    
    public function testUnbind(){
        $mock = $this->getSelfMock(array("delete"));
                $mock->expects($this->once())
                ->method("delete")
                ->with(array('userId=?'=>2,'aclGroupId=?'=>3))
                ->will($this->returnValue(1));
        
        $this->assertEquals(1,$mock->unBind(2,3));
        
    }

}
