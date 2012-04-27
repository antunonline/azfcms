<?php

class Azf_Service_Query_TokenizerTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var Azf_Service_Query_Tokenizer
     */
    protected $tokenizer = null;

    protected function setUp() {
        parent::setUp();
        $this->tokenizer = new Azf_Service_Query_Tokenizer();
    }

    protected function tokenize($expr) {
        return $this->tokenizer->tokenize($expr);
    }

    public function testNumber() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_NUMBER, "45")
        );

        $this->assertEquals($expected, $this->tokenize("45"));
    }

    public function testNumberWithSpacesBefore() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_WHITESPACE, ''),
            array(Azf_Service_Query_Tokenizer::T_NUMBER, "45")
        );

        $this->assertEquals($expected, $this->tokenize(" 45"));
    }

    public function testTokenizeDoubleQuotas() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_QUOTED_STRING, 'test')
        );

        $actual = $this->tokenize('"test"');
        $this->assertEquals($expected, $actual);
    }

    public function testTokenizeQuotas() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_QUOTED_STRING, 'test')
        );

        $actual = $this->tokenize("'test'");
        $this->assertEquals($expected, $actual);
    }
    public function testString() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_STRING, 'first')
        );

        $actual = $this->tokenize('first');

        $this->assertEquals($expected, $actual);
    }

    public function testStringWithNamespace() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_STRING, 'first'),
            '.',
            array(Azf_Service_Query_Tokenizer::T_STRING, 'last')
        );

        $actual = $this->tokenize('first.last');

        $this->assertEquals($expected, $actual);
    }

    public function testStringWithNamespaceAndMethodBrackets() {
        $expected = array(
            array(Azf_Service_Query_Tokenizer::T_STRING, 'first'),
            '.',
            array(Azf_Service_Query_Tokenizer::T_STRING, 'last'),
            '(', ')'
        );

        $actual = $this->tokenize('first.last()');

        $this->assertEquals($expected, $actual);
    }

    public function testMethodCallWithOneNumberArgument() {
        $expected = array(
        array(Azf_Service_Query_Tokenizer::T_STRING, 'first'),
        '.',
        array(Azf_Service_Query_Tokenizer::T_STRING, 'last'),
        '(',
        array(Azf_Service_Query_Tokenizer::T_NUMBER, "44"),
        ')'
        );

        $actual = $this->tokenize('first.last(44)');

        $this->assertEquals($expected, $actual);
    }

}
