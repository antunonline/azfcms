<?php

class Azf_Service_Lang_Validator {

    const C_ROOT = "croot";
    const C_ARRAY_VALUE = "avalue";
    const C_DICT_KEY = "dkey";
    const C_DICT_VALUE = "dvalue";
    const C_METHOD_PARAM = 'mparam';
    const C_METHOD_NAMESPACE = "cmethodnamespace";

    /**
     * Current context of the validator
     * @var array
     */
    protected $context = array(self::C_ROOT);

    /**
     * Current token value, populated only for complex types
     * @var string
     */
    protected $tValue = null;

    /**
     * Push context to validator
     * @param string $context 
     */
    public function pushContext($context) {
        $this->context[] = $context;
    }

    /**
     *  Pop context from validator
     *
     * @return string
     */
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

    public function getDataTokens($addTokens = null) {
        $tokens = array(
            Azf_Service_Lang_Tokenizer::T_NUMBER,
            Azf_Service_Lang_Tokenizer::T_QUOTED_STRING,
            Azf_Service_Lang_Tokenizer::T_STRING,
            '[', '{'
        );
        if (is_array($addTokens)) {
            $tokens = array_merge($tokens, $addTokens);
        }

        return $tokens;
    }

    public function getDictKeyTokens($addTokens = null) {
        $tokens = array(
            Azf_Service_Lang_Tokenizer::T_NUMBER,
            Azf_Service_Lang_Tokenizer::T_QUOTED_STRING
        );

        if (is_array($addTokens)) {
            $tokens = array_merge($tokens, $addTokens);
        }

        return $tokens;
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
            if (is_array($tokens[$i])) {
                $t = $tokens[$i][0];
                $this->tValue = $tokens[$i][1];
            } else {
                $t = $tokens[$i];
            }

            if ($t == Azf_Service_Lang_Tokenizer::T_WHITESPACE)
                continue;

            if (!$this->validateToken($bt, $t)) {
                throw new RuntimeException("Invalid token '$t' in front of '$bt'");
            }


            $bt = $t;
        }

        $this->validateToken($bt, "");

        if ($this->getCurrentContext() != self::C_ROOT) {
            throw new RuntimeException("Expression is not complete, context stack is not empty ".  implode(", ", $this->context));
        }
    }

    public function validateToken($bt, $t) {

        $isValid = false;
        switch ($bt) {
            case "":
                $isValid = $this->_validateInitial($t);
                break;
            case Azf_Service_Lang_Tokenizer::T_NUMBER:
                $isValid = $this->validateContext($t);
                break;
            case Azf_Service_Lang_Tokenizer::T_QUOTED_STRING:
                $isValid = $this->validateContext($t);
                break;
            case Azf_Service_Lang_Tokenizer::T_STRING:
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
        $isValid = false;
        switch ($this->getCurrentContext()) {
            case self::C_ARRAY_VALUE:
                $isValid = $this->_validateArrayValueContext($t);
                break;
            case self::C_DICT_KEY:
                $isValid = $this->_validateDictionaryKeyContext($t);
                break;
            case self::C_DICT_VALUE:
                $isValid = $this->_validateDictionaryValueContext($t);
                break;
            case self::C_METHOD_PARAM:
                $isValid = $this->_validateMethodParamContext($t);
                break;
            case self::C_ROOT:
                $isValid = $this->_validateRootContext($t);
                break;
        }

        return $isValid;
    }

    protected function inArray($t, $tokens) {
        $inArray = in_array($t, $tokens);

        // If token is found and token is t_string
        if ($inArray && $t == Azf_Service_Lang_Tokenizer::T_STRING) {

            // Check string types
            $valueLower = strtolower($this->tValue);
            switch ($valueLower) {
                case "null":
                    $keyword = true;
                    break;

                case "false":
                    $keyword = true;
                    break;

                case "true":
                    $keyword = true;
                    break;

                default:
                    $keyword = false;
                    break;
            }

            
            

            // If string is not keyword, enter into method namespace context
            if ($keyword == false && $this->getCurrentContext() != self::C_METHOD_NAMESPACE) {
                $this->pushContext(self::C_METHOD_NAMESPACE);
            }
        }

        return $inArray;
    }

    protected function _validateInitial($t) {
        return $this->inArray($t, array(
                    Azf_Service_Lang_Tokenizer::T_NUMBER,
                    Azf_Service_Lang_Tokenizer::T_QUOTED_STRING,
                    Azf_Service_Lang_Tokenizer::T_STRING,
                    '{', '['
                ));
    }

    /**
     * Nothing else than a single structure can occur in root context
     * @param type $t
     * @return boolean 
     */
    protected function _validateRootContext($t) {
        return false;
    }

    protected function _validateTString($t) {
        if($this->getCurrentContext()==self::C_METHOD_NAMESPACE){
            return $this->inArray($t, array(
                    '(', '.'
                ));
        } else {
            return $this->validateContext($t);
        }
    }

    protected function _validateTNumber($t) {
        return $this->validateContext($t);
    }

    protected function _validateTQuotedString($t) {
        return $this->validateContext($t);
    }

    protected function _validateNamespace($t) {
        return $t == Azf_Service_Lang_Tokenizer::T_STRING;
    }

    protected function _validateSeparator($t) {
        $isValid = false;

        switch ($this->getCurrentContext()) {
            case self::C_METHOD_PARAM:
                $isValid = $this->_validateMethodSeparator($t);
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
        // Pop namespace context
        $this->popContext();
        $this->pushContext(self::C_METHOD_PARAM);
        return $this->inArray($t, $this->getDataTokens(array(')')));
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
        return $this->inArray($t, array(
                    ',', ')'
                ));
    }

    protected function _validateMethodSeparator($t) {
        $valid = $this->inArray($t, $this->getDataTokens());
        return $valid;
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateOpenSquareBracket($t) {
        $this->pushContext(self::C_ARRAY_VALUE);
        return $this->inArray($t, $this->getDataTokens(array(']')));
    }

    /**
     * Start indexed array
     * @param string $t
     * @return boolean 
     */
    protected function _validateArrayValueContext($t) {
        return $this->inArray($t, array(',', ']'));
    }

    /**
     * Validate array separator
     * @param string $t
     * @return boolean 
     */
    protected function _validateArraySeparator($t) {
        return $this->inArray($t, $this->getDataTokens());
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
        return $this->inArray($t, $this->getDictKeyTokens(array('}')));
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
        return $this->inArray($t, array(":"));
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionaryValueBinding($t) {
        return $this->inArray($t, $this->getDataTokens());
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
        return $this->inArray($t, array(',', '}'));
    }

    /**
     * 
     * @param string $t
     * @return boolean 
     */
    protected function _validateDictionarySeparator($t) {
        return $this->inArray($t, $this->getDataTokens());
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
