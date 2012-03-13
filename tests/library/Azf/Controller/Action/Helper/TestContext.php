<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestContext
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Controller_Action_Helper_ContextTest extends PHPUnit_Framework_TestCase{
    
    public function getContextInstance(){
        $context = new Azf_Controller_Action_Helper_Context();
        return $context;
    }
    
    public function testGetStaticParam(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getStaticParam'),array(),'',false);
        $mock->expects($this->once())
                ->method("getStaticParam")
                ->with(33,"name","default")
                ->will($this->returnValue("return"));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals("return",$context->getStaticParam("name","default"));
    }
    
    
    public function testGetStaticParams(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getStaticParams'),array(),'',false);
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->with(33)
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getStaticParams("name"));
    }
    
    
    public function testGetDynamicParam(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getDynamicParam'),array(),'',false);
        $mock->expects($this->once())
                ->method("getDynamicParam")
                ->with(33,"name","default")
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getDynamicParam("name","default"));
    }
    
    
    public function testGetDynamicParams(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getDynamicParams'),array(),'',false);
        $mock->expects($this->once())
                ->method("getDynamicParams")
                ->with(33)
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getDynamicParams("name"));
    }
    
    
    public function testGetPluginParam(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getPluginParam'),array(),'',false);
        $mock->expects($this->once())
                ->method("getPluginParam")
                ->with(33,"plugin","name","default")
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getPluginParam("plugin","name","default"));
    }
    
    
    public function testGetPluginParams(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getPluginParams'),array(),'',false);
        $mock->expects($this->once())
                ->method("getPluginParams")
                ->with(33,"plugin")
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getPluginParams("plugin","name"));
    }
    
    
    public function testGetPluginNames(){
        $mock = $this->getMock("Azf_Model_Tree_Navigation",array('getPluginNames'),array(),'',false);
        $mock->expects($this->once())
                ->method("getPluginNames")
                ->with(33)
                ->will($this->returnValue(array(
                    'one'=>'two',
                    'three'=>"four"
                )));
        
        $context = new Azf_Controller_Action_Helper_Context();
        $context->setNavigationModel($mock);
        $context->setContextId(33);
        
        $this->assertEquals(array(
                    'one'=>'two',
                    'three'=>"four"
                ),$context->getPluginNames());
    }
    
    
}
