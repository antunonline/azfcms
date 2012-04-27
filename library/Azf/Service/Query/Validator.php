<?php

class Azf_Service_Query_Validator {

    const C_ROOT = "croot";
    const C_ARRAY_VALUE = "avalue";
    const C_DICT_KEY = "dkey";
    const C_DICT_VALUE = "dvalue";
    const C_METHOD_PARAM = 'mparam';

    protected $context = array(self::C_ROOT);

    public function pushContext($context) {
        $this->context[] = $context;
    }

    public function popContext() {
        return array_pop($this->context);
    }

    /**
     * This method will return current context of validated code.
     * @return string
     */
    public function getCurrentContext() {
        return $this->context[sizeof($this->context) - 1];
    }

    public function getDataTokens() {
        return array(
            Azf_Service_Query_Tokenizer::T_NUMBER,
            Azf_Service_Query_Tokenizer::T_QUOTED_STRING,
            Azf_Service_Query_Tokenizer::T_STRING,
            '[', '{'
        );
    }

    public function getDictKeyTokens() {
        return array(
            Azf_Service_Query_Tokenizer::T_NUMBER,
            Azf_Service_Query_Tokenizer::T_QUOTED_STRING
        );
    }

    /**
     * Validate tokens
     * @param array $tokens 
     * 
     */
    public function validate(array $tokens) {

        $i = 0;
        $len = sizeof($tokens);
        $bt = $t = null;

        for ($i = 0; $i < $len; $i++) {
            $t = $tokens[$i][0];

            if ($t == Azf_Service_Query_Tokenizer::T_WHITESPACE)
                continue;

            if (!$this->validateToken($bt, $t)) {
                throw new RuntimeException("Invalid token specified");
            }


            $bt = $t;
        }
    }

    public function validateToken($bt, $t) {

        $isValid = false;
        switch ($bt) {
            case "":
                $isValid = $this->_validateInitial($t);
                break;
            case Azf_Service_Query_Tokenizer::T_NUMBER:
                $isValid = $this->validateContext($t);
                break;
            case Azf_Service_Query_Tokenizer::T_QUOTED_STRING:
                $isValid = $this->validateContext($t);
                break;
            case Azf_Service_Query_Tokenizer::T_STRING:
                $isValid = $this->_validateTString($t);
                break;
            case ',':
                $isValid = $this->_validateSeparator($t);
                break;
            case '.':
                $isValid = $this->_validateMethodSeparator($t);
                break;
            case '(':
                $isValid = $this->_validateOpenParenthese($t);
                break;
            case ')':
                $isValid = $this->_validateCloseParenthese($t);
                break;
            case ')':
                $isValid = $this->_validateCloseParenthese($t);
                break;
            case '[':
                $isValid = $this->_validateOpenSquareBracket($t);
                break;
            case ']':
                $isValid = $this->_validateCloseSquareBracket($t);
                break;
            case '{':
                $isValid = $this->_validateOpenCurlyBracket($t);
                break;
            case '}':
                $isValid = $this->_validateCloseCurlyBracket($t);
                break;
            case ':':
                $isValid = $this->_validateDictionaryValueBinding($t);
                break;
            default:
                $isValid = false;
        }

        return $isValid;
    }

    protected function validateContext($t) {
        switch ($this->getCurrentContext()) {
            case self::C_ARRAY_VALUE:
                $this->_validateArrayValueContext($t);
                break;
            case self::C_DICT_KEY:
                $this->_validateDictionaryKeyContext($t);
                break;
            case self::C_DICT_VALUE:
                $this->_validateDictionaryValueContext($t);
                break;
            case self::C_METHOD_PARAM:
                $this->_validateMethodParamContext($t);
                break;
            case self::C_ROOT:
                $this->_validateRootContext($t);
                break;
        }
    }

    protected function _validateInitial($t) {
        return in_array($t, array(
                    Azf_Service_Query_Tokenizer::T_NUMBER,
                    Azf_Service_Query_Tokenizer::T_QUOTED_STRING,
                    Azf_Service_Query_Tokenizer::T_STRING,
                    '{', '['
                ));
    }
    
    /**
     * Nothing else than a single structure can occur in root context
     * @param type $t
     * @return boolean 
     */
    protected function _validateRootContext($t){
        return false;
    }

    protected function _validateTString($t) {
        return in_array($t, array(
                    '(', '.'
                ));
    }

    protected function _validateTNumber($t) {
        return $this->validateContext($t);
    }

    protected function _validateTQuotedString($t) {
        return $this->validateContext($t);
    }

    protected function _validateNamespace($t) {
        return $t == Azf_Service_Query_Tokenizer::T_STRING;
    }

    protected function _validateSeparator($t) {
        $isValid = false;
        
        switch($this->getCurrentContext()){
            case self::C_METHOD_PARAM: 
                $isValid = $this->_validateMethodParamContext($t);
                break;
            
            case self::C_ARRAY_VALUE:
                $isValid = $this->_validateArraySeparator($t);
                break;
            
            case self::C_DICT_KEY:
                $isValid = $this->_validateDictionarySeparator($t);
                break;
        }
        
        return $isValid;
    }

    /**
     * Start method paramter list
     * @param string $t
     * @return boolean 
     */
    protected function _validateOpenParenthese($t) {
        $this->pushContext(self::C_METHOD_PARAM);
        return in_array($t, $this->getDataTokens() + array(')'));
    }

    /**
     * End method paramter list
     * @param string $t
     * @return boolean 
     */
    protected function _validateCloseParenthese($t) {
        // Pop out of method value context
        $this->popContext();
        return $this->validateContext($t);
    }

    /**
     * This method will validate content that is placed after
     * data structure in method paramter list. 
     * @param string $t
     * @return boolean 
     */
    protected function _validateMethodParamContext($t) {
        // Replace current context with separator context
        $this->popContext();
        $this->pushContext(self::C_METHOD_SEPARATOR);
        return in_array($t, array(
                    ',', ')'
                ));
    }

    protected function _validateMethodSeparator($t) {
        $valid = in_array($t, $this->getDataTokens());
        return $valid;
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateOpenSquareBracket($t) {
        $this->pushContext(self::C_ARRAY_VALUE);
        return in_array($t, $this->getDataTokens());
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateArrayValueContext($t) {
        return in_array($t, array(',', ']'));
    }

    /**
     * Validate array separator
     * @param string $t
     * @return boolean 
     */
    protected function _validateArraySeparator($t) {
        return in_array($t, $this->getDataTokens());
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateCloseSquareBracket($t) {
        // Remove array value context
        $this->popContext();
        return $this->validateContext($t);
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateOpenCurlyBracket($t) {
        // push dictionary value context
        $this->pushContext(self::C_DICT_KEY);
        return in_array($t, $this->getDictKeyTokens());
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionaryKeyContext($t) {
        // Replace key context with value context
        $this->popContext();
        $this->pushContext(self::C_DICT_VALUE);
        return in_array($t, array(":"));
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionaryValueBinding($t) {
        return in_array($t, $this->getDataTokens());
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionaryValueContext($t) {
        // Replace value context with key context
        $this->popContext();
        $this->pushContext(self::C_DICT_KEY);
        return in_array($t, array(',', '}'));
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionarySeparator($t) {
        return in_array($t, $this->getDataTokens());
    }
    
    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateCloseCurlyBracket($t) {
        // Remove array value context
        $this->popContext();
        return $this->validateContext($t);
    }

}
