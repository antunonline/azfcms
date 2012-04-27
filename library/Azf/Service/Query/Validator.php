<?php

class Azf_Service_Query_Validator {

    const C_ROOT = "croot";
    const C_ARRAY_VALUE = "avalue";
    const C_DICT_KEY = "dkey";
    const C_DICT_VALUE = "dvalue";
    const C_METHOD_PARAM = 'mparam';
    const C_METHOD_SEPARATOR = 'mseparator';
    
    protected $context = array(self::C_ROOT);
    
    
    public function pushContext($context){
        $this->context[]=$context;
    }
    
    public function popContext(){
        return array_pop($this->context);
    }
    
    
    /**
     * This method will return current context of validated code.
     * @return string
     */
    public function getCurrentContext(){
        return $this->context[sizeof($this->context)-1];
    }
    
    
    public function getDataTokens(){
        return array(
            Azf_Service_Query_Tokenizer::T_NUMBER,
            Azf_Service_Query_Tokenizer::T_QUOTED_STRING,
            Azf_Service_Query_Tokenizer::T_STRING
        );
    }

    /**
     * Validate tokens
     * @param array $tokens 
     * 
     */
    public function validate(array $tokens) {

        $i = 0;
        $len = strlen($tokens);
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

        $isValid=false;
        switch ($bt) {
            case "":
                $isValid = $this->_validateInitial($t);
                break;
        }
        
        return $isValid;
    }
    
    
    protected function validateContext($t){
        switch($this->getCurrentContext()){
            case self::C_METHOD_PARAM:
                $this->_validateMethodParamContext($t);
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
    
    
    protected function _validateTString($t){
        return in_array($t, array(
                    '(', '.'
                ));
    }
    
    
    protected function _validateNamespace($t){
        return in_array($t, array(
                    Azf_Service_Query_Tokenizer::T_STRING
                ));
    }
    
    
    protected function _validateOpenParenthese($t){
        $this->pushContext(self::C_METHOD_PARAM);
        return in_array($t, array(
                    Azf_Service_Query_Tokenizer::T_STRING,
                    Azf_Service_Query_Tokenizer::T_NUMBER,
                    '{',   '[',   ')',
                    Azf_Service_Query_Tokenizer::T_QUOTED_STRING
                ));
    }
    
    
    protected function _validateCloseParenthese($t){
        return $this->validateContext($t);
    }
    
    protected function _validateMethodSeparator($t){
        
        $valid = false;
        switch($this->getCurrentContext()){
            case self::C_METHOD_SEPARATOR:
                $valid = in_array($t,$this->getDataTokens());
                break;
        }
        
        return $valid;
    }
    
    
    
    /**
     *
     * @param type $t
     * @return type 
     */
    protected function _validateMethodParamContext($t){
        $this->popContext();
        $this->pushContext(self::C_METHOD_SEPARATOR);
        return in_array($t,array(
            ',',')'
        ));
    }
    
    
    

}
