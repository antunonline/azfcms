<?php

class Azf_Service_Query_Parser {
    
    const TOKEN_NAME = 1;
    const TOKEN_METHOD_START = 2;
    const TOKEN_METHOD_END = 3;
    const TOKEN_NAMESPACE_SEPARATOR = 4;
    const TOKEN_ARRAY_START = 5;
    const TOKEN_ARRAY_END = 6;
    const TOKEN_ARRAY_SEPARATOR= 6;
    const TOKEN_ARRAY_SEPARATOR= 6;
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

    function __construct() {
        $this->setDescriptor(new Azf_Service_Query_Descriptor());
    }

    /**
     *
     * @param string $expression 
     */
    public function parse($expression) {
        
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