<?php

/**
 * Description of ACLGroupTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_ACLGroupTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
    }

    /**
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getSelfMock(array $methods) {
        return $this->getMock("Azf_Model_ACLGroup", $methods,array(),'',false);
    }

    public function testAddGroup() {
        $mock = $this->getSelfMock(array("insert"));
        $mock->expects($this->once())
                ->method("insert")
                ->with(array('name' => "SUPERADMIN"))
                ->will($this->returnValue(2));

        $this->assertEquals(2, $mock->addGroup("SUPERADMIN"));
    }

    public function testDeleteGroup() {
        $mock = $this->getSelfMock(array('delete'));
        $mock->expects($this->once())
                ->method("delete")
                ->with(array('id=?' => 11))
                ->will($this->returnValue(1));

        $this->assertEquals(1, $mock->deleteGroup(11));
    }

    /**
     * 
     */
    public function testGetGroups() {
        $dbAdapterMock = $this->getMock("Zend_Db_Adapter_Mysqli", array('fetchAll'), array(), '', false);
        $dbAdapterMock->expects($this->once())
                ->method("fetchAll")
                ->with("SELECT * FROM ACLGroup;")
                ->will($this->returnValue(array(
                            array(
                                'id' => 1,
                                'name' => "ONE"
                            ),
                            array(
                                'id' => 2,
                                'name' => "TWO"
                            )
                        )));

        $acl = new Azf_Model_ACLGroup(array('db' => $dbAdapterMock));
        $expected = array(
            array(
                'id' => 1,
                'name' => "ONE"
            ),
            array(
                'id' => 2,
                'name' => "TWO"
            )
        );

        $this->assertEquals($expected, $acl->getGroups());
    }

    public function testGetGroup() {
        $dbAdapterMock = $this->getMock("Zend_Db_Adapter_Mysqli", array('fetchRow'), array(), '', false);
        $dbAdapterMock->expects($this->once())
                ->method("fetchRow")
                ->with("SELECT * FROM ACLGroup WHERE id = ?;",array(321))
                ->will($this->returnValue(array(
                            'id' => 321,
                            'name' => "ONE"
                        )));

        $acl = new Azf_Model_ACLGroup(array('db' => $dbAdapterMock));
        $expected = array(
            'id' => 321,
            'name' => "ONE"
        );

        $this->assertEquals($expected, $acl->getGroup(321));
    }

}

