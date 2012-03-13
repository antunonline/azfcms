<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Default
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Controller_Router_Route_DefaultTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        
    }

    protected function setUp() {
        Azf_PHPUnit_Db_ConnectionFactory::initDefaultDbTableAdapter();
        parent::setUp();
    }

    /**
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getNavigationMock(array $methods) {
        return $this->getMock("Azf_Model_Tree_Navigation", $methods+array("__construct"));
    }

    /**
     * @param Azf_Model_Tree_Navigation $model 
     * @return Azf_Controller_Router_Route_Default
     */
    public function getRouteInstance($model) {
        return new Azf_Controller_Router_Route_Default($model);
    }

    /**
     * @test 
     */
    public function testMatch_homePage() {
        $mock = $this->getNavigationMock(array('getStaticParams','match'));
        $mock->expects($this->once())
                ->method("match")
                ->with(-1)
                ->will($this->returnValue("3"));
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->with($this->equalTo(3));
        

        $route = $this->getRouteInstance($mock);
        $route->match("");
    }

    /**
     * @test 
     */
    public function testMatch_pageId1() {
        $mock = $this->getNavigationMock(array('getStaticParams','match'));
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->with($this->equalTo(1));
        $mock->expects($this->once())
                ->method("match")
                ->with(1)
                ->will($this->returnValue(1));

        $route = $this->getRouteInstance($mock);
        $route->match("/this-is-cool/1.html");
    }

    /**
     * @test 
     */
    public function testMatch_pageId11() {
        $mock = $this->getNavigationMock(array('getStaticParams','match'));
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->with($this->equalTo(11));
        $mock->expects($this->once())
                ->method("match")
                ->will($this->returnValue(11));

        $route = $this->getRouteInstance($mock);
        $route->match("/this-is-cool/11.html");
    }

    /**
     * @test 
     */
    public function testMatch_pageId111() {
        $mock = $this->getNavigationMock(array('getStaticParams','match'));
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->with($this->equalTo(111));
        $mock->expects($this->once())
                ->method("match")
                ->with(111)
                ->will($this->returnValue(111));

        $route = $this->getRouteInstance($mock);
        $route->match("/this-is-cool/111.html?this=is&cool=yes");
    }
    
    /**
     * @test 
     */
    public function testMatch_HomePageMVCReturn() {
        $mock = $this->getNavigationMock(array('getStaticParams','match'));
        $mvc = array("module"=>"model","controller"=>"controller","action"=>"action",'id'=>3);
        $mock->expects($this->once())
                ->method("getStaticParams")
                ->will($this->returnValue($mvc));
        $mock->expects($this->once())
                ->method("match")
                ->will($this->returnValue(3));

        $route = $this->getRouteInstance($mock);
        $actual = $route->match("/");
        
        $this->assertEquals($mvc, $actual);  
   }
   
   /**
    *@test 
    */
   public function testMatch_assembleWihoutSEOSegment(){
       $mock = $this->getNavigationMock(array());
       $route = $this->getRouteInstance($mock);
       
       $expected = "/1.html";
       $actual = $route->assemble(array('id'=>1));
       $this->assertEquals($expected,$actual);
   }
   
   /**
    *@test 
    */
   public function testMatch_assembleWithSEOSegment(){
       $mock = $this->getNavigationMock(array());
       $route = $this->getRouteInstance($mock);
       
       $expected = "/SEO+KEYWORD/1.html";
       $actual = $route->assemble(array('id'=>1,'url'=>'SEO KEYWORD'));
       $this->assertEquals($expected,$actual);
   }
   
   
   /**
    *@test 
    */
   public function testMatch_assembleWithSEOSegmentAndQueryArgs(){
       $mock = $this->getNavigationMock(array());
       $route = $this->getRouteInstance($mock);
       
       $expected = "/SEO+KEYWORD/1.html?arg1=value1";
       $actual = $route->assemble(array('id'=>1,'url'=>'SEO KEYWORD','arg1'=>'value1'));
       $this->assertEquals($expected,$actual);
   }
   
   
   /**
    *@test 
    */
   public function testMatch_assembleWithMatchedValues(){
       $mock = $this->getNavigationMock(array('getStaticParams'));
       $mock->expects($this->once())
               ->method("getStaticParams")
               ->will($this->returnValue(array()));
       $route = $this->getRouteInstance($mock);
       $route->match("/SEO+KEYWORD/1.html?arg1=value1");
       
       $expected = "/SEO+KEYWORD/1.html?arg1=value1";
       $actual = $route->assemble(array('id'=>1,'url'=>'SEO KEYWORD'));
       $this->assertEquals($expected,$actual);
   }
   
   
   /**
    *@test 
    */
   public function testMatch_assembleWithoutMatchedValues(){
       $mock = $this->getNavigationMock(array('getStaticParams'));
       $mock->expects($this->once())
               ->method("getStaticParams")
               ->will($this->returnValue(array()));
       $route = $this->getRouteInstance($mock);
       $route->match("/SEO+KEYWORD/1.html");
       
       $expected = "/SEO+KEYWORD/1.html?arg1=value1";
       $actual = $route->assemble(array('id'=>1,'url'=>'SEO KEYWORD','arg1'=>'value1'),true);
       $this->assertEquals($expected,$actual);
   }

}
