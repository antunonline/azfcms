<?php

/**
 * Description of NavigationAclTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_NavigationAclTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
    }
    
    
    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getSelfMock(array $methods){
        return $this->getMock("Azf_Model_NavigationAcl",$methods,array(),"",false);
    }
    
    public function testBind(){
        $mock = $this->getSelfMock(array("insert"));
        $mock->expects($this->once())
                ->method("insert")
                ->with(array('navigationId'=>321,'aclGroupId'=>33))
                ->will($this->returnValue(3));
        
        $this->assertEquals(3,$mock->bind(321,33));
    }
    
    
    public function testUnBind(){
        $mock = $this->getSelfMock(array('delete'));
        $mock->expects($this->once())
                ->method("delete")
                ->with(array('navigationId=?'=>3,"aclGroupId=?"=>33))
                ->will($this->returnValue(1));
        
        $this->assertEquals(1,$mock->unBind(3,33));
    }

}
