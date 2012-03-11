<?php


/**
 * Description of UserTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Auth_Adapter_UserTest extends PHPUnit_Framework_TestCase{
    
    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getDbMock(){
        return $this->getMock("Azf_Model_User",array(),array(),'',false);
    }
    
    public function testValidAuthenticate(){
        $expected = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, array('loginName'=>'registered'));
        $dbMock = $this->getDbMock();
        $dbMock->expects($this->once())
                ->method("getUserByLoginName")
                ->will($this->returnValue(array(
                    'loginName'=>'registered',
                    'password'=>sha1('pass')
                )))
                ->with("registered");
        
       
        $auth = new Azf_Auth_Adapter_User($dbMock);
        $auth->setLoginName("registered");
        $auth->setPassword("pass");
        $result = $auth->authenticate();

        $this->assertEquals($expected->getCode(),$result->getCode());
        $this->assertEquals($expected->getIdentity(),$result->getIdentity());
        
    }
    
    
    public function testInvalidPassAuthenticate(){
        $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, array());
        $dbMock = $this->getDbMock();
                $dbMock->expects($this->once())
                ->method("getUserByLoginName")
                ->with("none")
                ->will($this->returnValue(array('loginName'=>'none','password'=>'shit')));
        
        $auth = new Azf_Auth_Adapter_User($dbMock);
        $auth->setLoginName("none");
        $auth->setPassword("none");
        
        $actual = $auth->authenticate();
        
        $this->assertEquals($result->getCode(), $actual->getCode());
        $this->assertEquals($result->getIdentity(), $actual->getIdentity());
    }
    
    
    public function testUnknownUserAuthenticate(){
        $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, array());
        $dbMock = $this->getDbMock();
                $dbMock->expects($this->once())
                ->method("getUserByLoginName")
                ->with("none")
                ->will($this->returnValue(array()));
        
        $auth = new Azf_Auth_Adapter_User($dbMock);
        $auth->setLoginName("none");
        $auth->setPassword("none");
        
        $actual = $auth->authenticate();
        
        $this->assertEquals($result->getCode(), $actual->getCode());
        $this->assertEquals($result->getIdentity(), $actual->getIdentity());
    }
}

