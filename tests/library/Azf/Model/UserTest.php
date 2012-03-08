<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_UserTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
    }
    
    /**
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getSelfMock(array $methods){
        return $this->getMock("Azf_Model_User",$methods,array(),'',false);
    }
    
    public function testAddUser(){
        $user = array(
            'loginName'=>"login",
            'password'=>sha1('pass'),
            'firstName'=>"fn",
            'lastName'=>'ln',
            'email'=>'net@hr.'
        );
        
        $mock = $this->getSelfMock(array('insert'));
        $mock->expects($this->once())
                ->method("insert")
                ->with()
                ->will($this->returnArgument(0));
        
        $actual = $mock->createUser("login","pass","fn","ln","net@hr.");
        $this->assertEquals(32,strlen($actual['verificationKey']));
        unset($actual['verificationKey']);
        $this->assertEquals($user,$actual);
    }
    
    
    public function testGetValidationKey(){
        $dbMock = $this->getMock("Zend_Db_Adapter_Mysqli",array('fetchOne'),array(),'',false);
        $dbMock->expects($this->once())
                ->method("fetchOne")
                ->with("SELECT validationKey FROM User WHERE id = ?;",array(33))
                ->will($this->returnValue("5"));
        
        $user = new Azf_Model_User(array("db"=>$dbMock));
        $this->assertEquals("5",$user->getValidationKey(33));
    }
    
    
    public function testVerifyUser(){
        $dbMock = $this->getMock("Zend_Db_Adapter_Mysqli",array('update'),array(),'',false);
        $dbMock->expects($this->once())
                ->method("update")
                ->with("User",array('verified'=>1),array('verificationKey=?'=>33))
                ->will($this->returnValue("1"));
        
        $user = new Azf_Model_User(array("db"=>$dbMock));
        $this->assertTrue($user->verifyUser(33));
    }
    
    
    public function testSetUserFirstName(){
        $self = $this->getSelfMock(array('update'));
        $self->expects($this->once())
                ->method("update")
                ->with(array('firstName'=>'abc'),array('id=?'=>33))
                ->will($this->returnValue(1));
        
        $this->assertTrue($self->setUserFirstName(33,'abc'));
    }
    
    
    public function testSetUserLastName(){
        $self = $this->getSelfMock(array('update'));
        $self->expects($this->once())
                ->method("update")
                ->with(array('lastName'=>'abc'),array('id=?'=>33))
                ->will($this->returnValue(1));
        
        $this->assertTrue($self->setUserLastName(33,'abc'));
    }
    
    
    public function testSetUserPassword(){
        $self = $this->getSelfMock(array('update'));
        $self->expects($this->once())
                ->method("update")
                ->with(array('password'=>sha1('abc')),array('id=?'=>33))
                ->will($this->returnValue(1));
        
        $this->assertTrue($self->setUserPassword(33,'abc'));
    }
    
    
    public function testDeleteUser(){
        $self = $this->getSelfMock(array('delete'));
        $self->expects($this->once())
                ->method("delete")
                ->with(array('id=?'=>33))
                ->will($this->returnValue(1));
        
        $this->assertTrue($self->deleteUser(33));
    }
    
    
    public function testGetUser(){
        $expected = array(
            'id'=>33,
            'loginName'=>'ln',
            'password'=>sha1("pass"),
            'firstName'=>"fn",
            'lastName'=>'ln',
            'email'=>'mail@example.com',
            'rTime'=>0,
            'rTime'=>0,
            'verified'=>1,
            'verificationKey='=>md5(1)
            
        );
        
        $dbMock = $this->getMock("Zend_Db_Adapter_Mysqli",array('fetchRow'),array(),'',false);
        $dbMock->expects($this->once())
                ->method("fetchRow")
                ->with("SELECT * FROM User WHERE id = ?;",array(33))
                ->will($this->returnValue($expected));
        
        $user = new Azf_Model_User(array("db"=>$dbMock));
        $this->assertEquals($expected,$user->getUser(33));
    }
    
    
    public function testGetUserByLoginName(){
        $expected = array(
            'id'=>33,
            'loginName'=>'ln',
            'password'=>sha1("pass"),
            'firstName'=>"fn",
            'lastName'=>'ln',
            'email'=>'mail@example.com',
            'rTime'=>0,
            'rTime'=>0,
            'verified'=>1,
            'verificationKey='=>md5(1)
            
        );
        
        $dbMock = $this->getMock("Zend_Db_Adapter_Mysqli",array('fetchRow'),array(),'',false);
        $dbMock->expects($this->once())
                ->method("fetchRow")
                ->with("SELECT * FROM User WHERE loginName = ?;",array("ln"))
                ->will($this->returnValue($expected));
        
        $user = new Azf_Model_User(array("db"=>$dbMock));
        $this->assertEquals($expected,$user->getUserByLoginName("ln"));
    }

}
