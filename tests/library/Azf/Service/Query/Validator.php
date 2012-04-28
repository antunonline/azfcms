<?php

class Azf_Service_Query_ValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var Azf_Service_Query_Validator
     */
    protected $validator;

    protected function setUp() {
        parent::setUp();

        $this->validator = new Azf_Service_Query_Validator();
    }

    public function tokenize($query) {
        return Azf_Service_Query_Tokenizer::getInstance()
                        ->tokenize($query);
    }

    public function validate($query) {
        $this->validator->validate($this->tokenize($query));
        return true;
    }
    
    public function testEmptyExpr(){
        $this->validator->validate($this->tokenize(""));
    }

    public function testValidateTNumber() {
        $this->assertTrue($this->validate("33"));
    }

    public function testValidateTDoubleQuotas() {
        $this->assertTrue($this->validate('"test"'));
    }

    public function testMethodCall() {
        $this->assertTrue($this->validate("user.info()"));
    }

    public function testMethodCallWithNumArgument() {
        $this->assertTrue($this->validate("user.info(44)"));
    }

    public function testMethodCallWithNumAndQuoteArgument() {
        $this->assertTrue($this->validate("user.info(44,\"WDF\")"));
    }

    public function testMethodCallWithNumQuoteArgumentAndDict() {
        $this->assertTrue($this->validate("user.info(44,\"WDF\",{44:44})"));
    }

    public function testMethodUnclosedMethodCall() {
        $this->setExpectedException("RuntimeException");
        $this->validate("this.wdf(44,44,{44:44},[44],");
    }

    public function testEmptyArray() {
        $this->assertTrue($this->validate("[]"));
    }

    public function testUnclosedArray() {
        $this->setExpectedException("RuntimeException");
        $this->validate("[");
    }

    public function testArrayWithNumber() {
        $this->assertTrue($this->validate("[44]"));
    }

    public function testArrayWithQuotedString() {
        $this->assertTrue($this->validate("[\"YES\"]"));
    }

    public function testArrayWithQuotedStringAndNumber() {
        $this->assertTrue($this->validate("[\"YES\",44]"));
    }

    public function testArrayWithCall() {
        $this->assertTrue($this->validate("[this()]"));
    }

    public function testEmptyDict() {
        $this->assertTrue($this->validate("{}"));
    }

    public function testUnclosedDict() {
        $this->setExpectedException("RuntimeException");
        $this->validate("{");
    }

    public function testDictWithNumber() {
        $this->assertTrue($this->validate("{44:44}"));
    }

    public function testDictWithQuotedString() {
        $this->assertTrue($this->validate("{\"KEY\":\"VALUE\"}"));
    }

    public function testDictWithMethodCall() {
        $this->assertTrue($this->validate("{\"KEY\":this.one()}"));
    }

    public function testDictWithMethodCallAndOtherKey() {
        $this->assertTrue($this->validate("{\"KEY\":this.one(),44:\"t\"}"));
    }
    
    public function testLegalComplexExpr1(){
        $this->assertTrue($this->validate("{'myName':user.getLoginName(),'email':user.getLoginPassword()}"));
    }

    public function testIllegalComplexExpr1() {
        $this->setExpectedException("RuntimeException");
        $this->validate("{
 0:myName(),
 1: hisName({'first','last'}),
}");
    }

}
