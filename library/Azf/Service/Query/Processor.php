<?php

class Azf_Service_Query_Processor {

    const C_INITIALL = "cinitiall";
    const C_DICT = "cdict";
    const C_DICT_KEY = 'dictkey';
    const C_DICT_VALUE = "dictvalue";
    const C_ARRAY = "carray";
    const C_METHOD_NAMESPACE = 'cmethodnamespace';
    const C_METHOD_PARAMETERS = "cmethodparameters";

    /**
     * Structure that 
     * @var array
     */
    protected $context = array(self::C_INITIALL);

    /**
     * Structure that
     * @var array
     */
    protected $data = array();

    /**
     *
     * @param string $context
     * @param array $data 
     */
    protected function pushContext($context, array &$data) {
        $this->context[] = array($context, $data);
    }

    /**
     *
     * @return array
     */
    protected function &popContext() {
        $context =  &array_pop($this->context);
        return $context;
    }

    /**
     *
     * @return array
     */
    public function &getCurrentContext() {
        return $this->context[sizeof($this->context) - 1];
    }

    protected function addValue(&$value) {
        $context = &$this->getCurrentContext();

        switch ($context[0]) {
            case self::C_DICT:
                $this->_addDictKey($value, $context);
                break;
            case self::C_DICT_KEY:
                $this->_addDictValue($value, $context);
                break;
            case self::C_ARRAY:
                $this->_addArrayValue($value, $context);
                break;
            case self::C_METHOD_NAMESPACE:
                $this->_addMethodNamespace($value, $context);
                break;
            case self::C_METHOD_PARAMETERS:
                $this->_addMethodparameter($value, $context);
                break;
            case self::C_INITIALL:
                $this->_addInitialValue($value, $context);
                break;
        }
    }

    protected function _addArrayValue(&$value, &$context) {
        $context[1][] = &$value;
    }

    protected function _addDictKey(&$value, &$context) {
        $data = array(&$value);
        $this->pushContext(self::C_DICT_KEY, $data);
    }

    protected function _addDictValue(&$value, &$context) {
        // Pop key context
        $keyContext = $this->popContext();
        $key = $keyContext[1][0];
        // Get current dict context
        $dictContext = &$this->getCurrentContext();
        $dict = &$dictContext[1];
        // Add key and value to dict 
        $dict[$key] = &$value;
    }

    protected function _addMethodNamespace(&$value, &$context) {
        $context[1]['namespace'][] = $value;
    }

    protected function _addMethodparameter(&$value, &$context) {
        $context[1]['parameters'][] = &$value;
    }
    
    protected function _addInitialValue(&$value,&$context){
        $context[1][] =&$value;
    }

    protected function _reset() {
        // Reset context
        $data = array();
        $this->data = &$data;
        $this->context = array(array(self::C_INITIALL, &$data));
    }

    protected function _executeMethod(array $metadata) {
        
    }

    public function process(array $tokens) {
        $len = sizeof($tokens);
        $t = null;

        $this->_reset();

        for ($i = 0; $i < $len; $i++) {
            $t = $tokens[$i];
            $this->_process($t);
        }
        
        return isset($this->data[0])?$this->data[0]:"";
    }

    protected function _process($t) {
        $context = &$this->getCurrentContext();
        if (is_array($t)) {
            $this->_processArray($t, $context);
        } else {
            $this->_processChar($t, $context);
        }
    }

    protected function _processChar($t, array &$context) {
        switch ($t) {
            case "(":
                $this->_processOpenParenthese($context);
                break;
            case ")":
                $this->_processCloseParenthese($context);
                break;
            case "[":
                $this->_processOpenSquareBracket($context);
                break;
            case "]":
                $this->_processCloseSquareBracket($context);
                break;
            case "{":
                $this->_processOpenCurlyBracket($context);
                break;
            case "}":
                $this->_processCloseCurlyBracket($context);
                break;
        }
    }

    protected function _processArray(array $t, array &$context) {
        $type = $t[0];
        $value = $t[1];

        switch ($type) {
            case Azf_Service_Query_Tokenizer::T_NUMBER:
                $this->_processNumber($value, $context);
                break;
            case Azf_Service_Query_Tokenizer::T_QUOTED_STRING:
                $this->_processQuotedString($value, $context);
                break;
            case Azf_Service_Query_Tokenizer::T_WHITESPACE:
                
                break;
            case Azf_Service_Query_Tokenizer::T_STRING:
                $this->_processString($value, $context);
                break;
            
        }
    }

    protected function _processString($string, array &$context) {

        if ($context[0] == self::C_METHOD_NAMESPACE) {
            
        } else {
            $dataStructure = array(
                'namespace' => array(),
                'parameters' => array()
            );

            $this->pushContext(self::C_METHOD_NAMESPACE, $dataStructure);
        }

        $this->addValue($string);
    }

    protected function _processNumber($number, &$context) {
        $this->addValue($number);
    }

    protected function _processQuotedString($string, &$context) {
        $this->addValue($string);
    }

    protected function _processOpenParenthese(array &$context) {
        // Add method context
        $this->pushContext(self::C_METHOD_PARAMETERS, $context[1]);
    }

    protected function _processCloseParenthese(array &$context) {
        // Pop put method and namespace context
        $this->popContext();
        $this->popContext();

        // Execute method
        $methodMetadata = $context[1];
        $data = $this->_executeMethod($methodMetadata);

        // Store data
        $this->addValue($data);
    }

    protected function _processOpenSquareBracket(array &$context) {
        $data = array();
        $this->pushContext(self::C_ARRAY, $data);
    }

    protected function _processCloseSquareBracket(array &$context) {
        // Pop array context
        $this->popContext();
        $data = &$context[1];
        $this->addValue($data);
    }

    protected function _processOpenCurlyBracket(array &$context) {
        $data = array();
        $this->pushContext(self::C_DICT, $data);
    }

    protected function _processCloseCurlyBracket(array &$context) {
        // Pop dict context
        $this->popContext();

        $data = &$context[1];
        // Store value
        $this->addValue($data);
    }

}
