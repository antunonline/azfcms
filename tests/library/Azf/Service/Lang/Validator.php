<?php

class Azf_Service_Lang_ValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var Azf_Service_Lang_Validator
     */
    protected $validator;

    protected function setUp() {
        parent::setUp();

        $this->validator = new Azf_Service_Lang_Validator();
    }

    public function tokenize($query) {
        return Azf_Service_Lang_Tokenizer::getInstance()
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

    public function testValidateBooleanFalse() {
        $this->assertTrue($this->validate("false"));
    }

    public function testValidateBooleanTrue() {
        $this->assertTrue($this->validate("true"));
    }

    public function testValidateNull() {
        $this->assertTrue($this->validate("null"));
    }

    public function testValidateInvalidBooleanFalseCall() {
        $this->setExpectedException("RuntimeException");
        $this->validate("false()");
    }

    public function testValidateInvalidBooleanTrueCall() {
        $this->setExpectedException("RuntimeException");
        $this->validate("true()");
    }

    public function testValidateInvalidNullCall() {
        $this->setExpectedException("RuntimeException");
        $this->validate("null()");
    }

    public function testValidateInvalidString() {
        $this->setExpectedException("RuntimeException");
        $this->validate("someinvalidstring");
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
    
    public function testLegalComplexExpr2(){
        $this->validate("{44:false}");
    }
    
    public function testLegalComplexExpr3(){
        $this->validate("[compare(true,true),compare(true,false),compare(false,null)]");
    }

    public function testIllegalComplexExpr1() {
        $this->setExpectedException("RuntimeException");
        $this->validate("{
 0:myName(),
 1: hisName({'first','last'}),
}");
    }

    
    /**
     * Here we have defined simple dictionary where key of the value is string '44' and
     * the value is unfinished method declaration 'this'. Since 'this' is not a boolean
     * or a null value, the expression is invalid
     */
    public function testIllegalComplexExpr2() {
        $this->setExpectedException("RuntimeException");
        $this->validate("{'44':this}");
    }

}
