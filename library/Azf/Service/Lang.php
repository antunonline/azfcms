<?php

class Azf_Service_Lang {

    /**
     *
     * @var array
     */
    protected $resolvers = array();

    /**
     *
     * @var Azf_Service_Lang_Tokenizer
     */
    protected $tokenizer = null;

    /**
     *
     * @var Azf_Service_Lang_Validator
     */
    protected $validator = null;

    /**
     *
     * @var Azf_Service_Lang_Processor
     */
    protected $processor = null;

    public function getResolvers() {
        return $this->resolvers;
    }

    public function setResolvers($resolvers) {
        $this->resolvers = $resolvers;
    }

    /**
     *
     * @param string $namespace
     * @param Azf_Service_Lang_Resolver|string $resolver 
     */
    public function setResolver($namespace, $resolver) {
        $this->resolvers[$namespace] = $resolver;
    }

    public function getTokenizer() {
        return $this->tokenizer;
    }

    public function setTokenizer($tokenizer) {
        $this->tokenizer = $tokenizer;
    }

    public function getValidator() {
        return $this->validator;
    }

    public function setValidator($validator) {
        $this->validator = $validator;
    }

    public function getProcessor() {
        return $this->processor;
    }

    public function setProcessor($processor) {
        $this->processor = $processor;
    }

    protected function _initializeTokenizer() {
        if ($this->getTokenizer() == null) {
            $this->setTokenizer(new Azf_Service_Lang_Tokenizer());
        }
    }

    protected function _initializeValidator() {
        if ($this->getValidator() == null) {
            $this->setValidator(new Azf_Service_Lang_Validator());
        }
    }

    protected function _initializeProcessor() {
        if ($this->getProcessor() == null) {
            $processor = new Azf_Service_Lang_Processor();
            $processor->setResolvers($this->getResolvers());
            $this->setProcessor($processor);
        }
    }

    /**
     *
     * @param string $expression
     * @return mixed 
     */
    public function execute($expression) {
        if (!is_string($expression)) {
            throw new InvalidArgumentException("Lang expression is not a string");
        }

        $this->_initializeTokenizer();
        $tokens = $this->getTokenizer()->tokenize($expression);

        $this->_initializeValidator();
        try {
            $this->getValidator()->validate($tokens);
            $this->_initializeProcessor();

            return $this->getProcessor()->process($tokens);
        } catch (RuntimeException $e) { 
            return "Syntax error";
        }
    }

    /**
     *
     * @param string $expression
     * @return string 
     */
    public function executeAndJson($expr) {
        return json_encode($this->execute($expr));
    }

}