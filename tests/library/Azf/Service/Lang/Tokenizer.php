<?php

class Azf_Service_Lang_TokenizerTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var Azf_Service_Lang_Tokenizer
     */
    protected $tokenizer = null;

    protected function setUp() {
        parent::setUp();
        $this->tokenizer = new Azf_Service_Lang_Tokenizer();
    }

    protected function tokenize($expr) {
        return $this->tokenizer->tokenize($expr);
    }

    public function testNumber() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, "45")
        );

        $this->assertEquals($expected, $this->tokenize("45"));
    }

    public function testNetagiveNumber() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, "-45")
        );

        $this->assertEquals($expected, $this->tokenize("-45"));
    }
    
    
    public function testFloatingPointNumber(){
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, 0.185)
        );
        
        $this->assertEquals($expected, $this->tokenize("0.185"));
    }
    
    
    public function testNetagiveFloatingPointNumber(){
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, -0.185)
        );
        
        $this->assertEquals($expected, $this->tokenize("-0.185"));
    }
    
    
    public function testIllegalDoubleDottedFloatingPointNumber(){
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, 0.185),
            '.',
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, 33),
        );
        
        $this->assertEquals($expected, $this->tokenize("0.185.33"));
    }

    public function testNumberWithSpacesBefore() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_WHITESPACE, ''),
            array(Azf_Service_Lang_Tokenizer::T_NUMBER, "45")
        );

        $this->assertEquals($expected, $this->tokenize(" 45"));
    }

    public function testTokenizeDoubleQuotas() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_QUOTED_STRING, 'test')
        );

        $actual = $this->tokenize('"test"');
        $this->assertEquals($expected, $actual);
    }

    public function testTokenizeQuotas() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_QUOTED_STRING, 'test')
        );

        $actual = $this->tokenize("'test'");
        $this->assertEquals($expected, $actual);
    }
    public function testString() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_STRING, 'first')
        );

        $actual = $this->tokenize('first');

        $this->assertEquals($expected, $actual);
    }

    public function testStringWithNamespace() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_STRING, 'first'),
            '.',
            array(Azf_Service_Lang_Tokenizer::T_STRING, 'last')
        );

        $actual = $this->tokenize('first.last');

        $this->assertEquals($expected, $actual);
    }

    public function testStringWithNamespaceAndMethodBrackets() {
        $expected = array(
            array(Azf_Service_Lang_Tokenizer::T_STRING, 'first'),
            '.',
            array(Azf_Service_Lang_Tokenizer::T_STRING, 'last'),
            '(', ')'
        );

        $actual = $this->tokenize('first.last()');

        $this->assertEquals($expected, $actual);
    }

    public function testMethodCallWithOneNumberArgument() {
        $expected = array(
        array(Azf_Service_Lang_Tokenizer::T_STRING, 'first'),
        '.',
        array(Azf_Service_Lang_Tokenizer::T_STRING, 'last'),
        '(',
        array(Azf_Service_Lang_Tokenizer::T_NUMBER, "44"),
        ')'
        );

        $actual = $this->tokenize('first.last(44)');

        $this->assertEquals($expected, $actual);
    }

}
