<?php

class Azf_Service_Query_ProcessorTest extends PHPUnit_Framework_TestCase {
    
    
    
    
    protected function getResolverMock($methods = array()){
        return $this->getMock("Azf_Service_Query_Resolver", $methods);
    }
    
    protected function tokenize($expression){
        return Azf_Service_Query_Tokenizer::getInstance()
                ->tokenize($expression);
    }
    
    protected function validate(array $tokens){
        $validator = new Azf_Service_Query_Validator();
        return $validator->validate($tokens);
    }
    
    
    protected function execute(array $tokens, $resolvers){
        $processor = new Azf_Service_Query_Processor();
        foreach($resolvers as $namespace=>$resolver){
            $processor->addResolver($namespace, $resolver);
        }
        
        return $processor->process($tokens);
    }
    
    protected function process($expr, $resolvers=array()){
        $tokens = $this->tokenize($expr);
        $this->validate($tokens);
        return $this->execute($tokens, $resolvers);
    }
    
    
    public function testHelloWorld(){
        $actual = $this->process("'hello world'");
        $expected = "hello world";
        
        $this->assertEquals($expected, $actual);
    }
    
    
    public function testEmptyExpr(){
        $actual = $this->process("");
        $expected = "";
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testNumber(){
        $actual = $this->process("44");
         $expected = "44";
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testNetativeNumber(){
        $actual = $this->process("-44");
         $expected = -44;
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testSingleQuotedString(){
        $actual = $this->process("'wdf'");
         $expected = "wdf";
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testDoubleQuotedString(){
        $actual = $this->process("\"wdf\"");
         $expected = "wdf";
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testBooleanFalse(){
        $actual = $this->process("false");
         $expected = false;
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testBooleanTrue(){
        $actual = $this->process("true");
         $expected = true;
        
        $this->assertEquals($expected ,$actual);
    }
    
    
    public function testBooleanNull(){
        $actual = $this->process("null");
         $expected = null;
        
        $this->assertEquals($expected ,$actual);
    }
    
    public function testEmptyArray(){
        $actual = $this->process("[]");
         $expected = array();
        
        $this->assertEquals($expected,$actual);
    }
    
    public function testEmptyDict(){
        $actual = $this->process("{}");
         $expected = array();
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testArrayWithNumber(){
        $actual = $this->process("[44]");
         $expected = array("44");
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testArrayWithSingleQuotedString(){
        $actual = $this->process("['this is cool']");
         $expected = array("this is cool");
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testArrayWithDobuleQuotedString(){
        $actual = $this->process("[\"this is cool\"]");
         $expected = array("this is cool");
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testArrayWithBooleanValuesAndNulles(){
        $actual = $this->process("[false,false,true,null,true]");
         $expected = array(false,false,true,null,true);
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testOneDimensionalArray(){
        $actual = $this->process("[[],[]]");
         $expected = array(
             array(),
             array()
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testOneDimensionalArrayWithQuotedStrings(){
        $actual = $this->process("[['is cool'],['this']]");
         $expected = array(
             array('is cool'),
             array('this')
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testArrayWithTwoDicts(){
        $actual = $this->process("[{},{}]");
         $expected = array(
             array(),
             array()
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithNumberKey(){
        $actual = $this->process("{44:44}");
         $expected = array(
             "44"=>"44"
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithSingleQuotedKey(){
        $actual = $this->process("{'44':44}");
         $expected = array(
             "44"=>"44"
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithDoubleQuotedKey(){
        $actual = $this->process("{\"44\":44}");
         $expected = array(
             "44"=>"44"
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithArrayAsValue(){
        $actual = $this->process("{\"44\":[]}");
         $expected = array(
             "44"=>array()
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithDictAsValue(){
        $actual = $this->process("{\"44\":{}}");
         $expected = array(
             "44"=>array()
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testDictWithTwoKeys(){
        $actual = $this->process("{\"44\":{},66:66}");
         $expected = array(
             "44"=>array(),
             '66'=>'66'
         );
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParameters(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->with(array(),array());
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf()",$resolvers);
         $expected = null;
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParametersWithStringReturn(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->with(array(),array())
                ->will($this->returnValue("WORKS"));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf()",$resolvers);
         $expected = "WORKS";
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParametersWithNumberArgument(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->will($this->returnArgument(1));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf(44)",$resolvers);
         $expected = array("44");
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParametersWithArrayArgument(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->will($this->returnArgument(1));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf([])",$resolvers);
         $expected = array(array());
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParametersWithDictArgument(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->will($this->returnArgument(1));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf({})",$resolvers);
         $expected = array(array());
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWithoutParametersWithPopulatedDictArgument(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->will($this->returnArgument(1));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf({44:44})",$resolvers);
         $expected = array(array('44'=>'44'));
        
        $this->assertEquals($expected,$actual);
    }
    
    
    public function testCallWIthBooleanAndNullParameters(){
        $resolver = $this->getResolverMock(array("_execute"));
        $resolver->expects($this->once())
                ->method("_execute")
                ->with(array(),array(false,true,null))
                ->will($this->returnArgument(1));
        
        $resolvers = array('wdf'=>$resolver);
        
        $actual = $this->process("wdf(false,true,null)",$resolvers);
         $expected = array(false,true,null);
        
        $this->assertEquals($expected,$actual);
    }
    
    
}