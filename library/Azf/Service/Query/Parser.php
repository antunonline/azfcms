<?php

class Azf_Service_Query_Parser {

    /**
     * alphanumeric name that must not start with a number 
     */
    const TOKEN_NAME = 1;
    /**
     * Open bracket 
     */
    const TOKEN_METHOD_START = 2;
    /**
     * Closing bracket 
     */
    const TOKEN_METHOD_END = 3;
    /**
     * A dot 
     */
    const TOKEN_NAMESPACE_SEPARATOR = 4;
    /**
     * Open bracket { 
     */
    const TOKEN_ARRAY_START = 5;
    /**
     * Closing bracket } 
     */
    const TOKEN_ARRAY_END = 6;
    /**
     * comma , 
     */
    const TOKEN_ARRAY_SEPARATOR = 6;
    /**
     *  Text between double qoutes 
     */
    const TOKEN_QUOTED_STRING = 7;

    /**
     *
     * @var Azf_Service_Query_Descriptor
     */
    protected $descriptor;

    /**
     *
     * @var string
     */
    protected $token;

    /**
     *
     * @var int
     */
    protected $tokenType;

    /**
     *
     * @var string
     */
    protected $expression;
    
    protected $expLen;
    
    protected $pos = 0;

    public function getDescriptor() {
        return $this->descriptor;
    }

    public function setDescriptor($descriptor) {
        $this->descriptor = $descriptor;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getTokenType() {
        return $this->tokenType;
    }

    public function setTokenType($tokenType) {
        $this->tokenType = $tokenType;
    }

    public function getExpression() {
        return $this->expression;
    }

    public function setExpression($expression) {
        $this->expression = $expression;
    }

    function __construct() {
        $this->setDescriptor(new Azf_Service_Query_Descriptor());
    }

    /**
     *
     * @param string $expression 
     */
    public function parse($expression) {
        $this->setExpression($expression);
        $this->expLen = strlen($expression);
    }

    /**
     *  @return boolean
     */
    protected function parseNext() {
        
    }

    /**
     *  
     */
    protected function _parseExpression() {

        $descriptor = $this->getDescriptor();
        while ($this->parseNext()) {
            
        }
    }

}