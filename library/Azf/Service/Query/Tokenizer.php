<?php

class Azf_Service_Query_Tokenizer {

    CONST T_STRING = "STRING";
    const T_WHITESPACE = "WHITESPACE";
    const T_NUMBER = "NUMBER";
    const T_QUOTED_STRING = "QUOTEDSTRING";
    
    
    /**
     *
     * @var Azf_Service_Query_Tokenizer
     */
    protected static $instance;

    protected $query;
    protected $pos = 0;
    protected $len = 0;

    /**
     *
     * @param string $query 
     * @return array
     */
    public function tokenize($query) {
        $this->pos = 0;
        $this->query = $query;
        $this->len = strlen($query);

        $tokens = array();
        while (null !== ($token = $this->nextToken())) {
//            echo "POS: $this->pos, LEN: $this->len\n";
            $tokens[] = $token;
        }
        return $tokens;
    }

    public function nextToken() {
        // If there is not more tokens, return null
        if ($this->pos < $this->len == false) {
            return null;
        }

        $return = $this->getString();
        if ($return !== null) {
                return array(self::T_STRING, $return);
            }
            
        $return = $this->getQuotedString();
        if ($return !== null) {
            return array(self::T_QUOTED_STRING, $return);
        }

        $return = $this->getDQuotedString();
        if ($return !== null) {
            return array(self::T_QUOTED_STRING, $return);
        }
        
        $return = $this->getNumber();
        if($return !== null){
            return array(self::T_NUMBER, $return);
        }
        
        $return = $this->getWhitespace();
        if($return !==null){
            return array(self::T_WHITESPACE,"");
        }

        // Return character if not recognized as a token
        $return = $this->query[$this->pos];
        $this->pos++;
        return $return;
    }

    protected function getString() {
        $ord = 0;
        $pos = $this->pos;
        $c = "";
        $string = "";

        for (; $pos < $this->len; $pos++) {
            $c = $this->query[$pos];
            $ord = ord($c);

            // Is lower case or upper case letter
            if (($ord > 96 && $ord < 123 ) ||
                    ( $ord > 64 && $ord < 91)) {
                $string .= $c;
            } else if ($ord > 47 && $ord < 58 && isset($string[0])) {
                $string .= $c;
            } else {
                break;
            }
        }
        // Store current position
        $this->pos = $pos;

        // Return string
        if (isset($string[0])) {
            return $string;
        } else {
            return null;
        }
    }

    protected function getQuotedString() {
        $pos = $this->pos;
        $c = $this->query[$pos];
        $ord = ord($c);

        if ($ord != 39) { 
            return null;
        } else {
            $pos++;
        }

        $string = "";
        for (; $pos < $this->len; $pos++) {
            $c = $this->query[$pos];
            $ord = ord($c);

            // If backslash is found
            if ($ord == 92) {
                // Has next character
                if (($pos + 1) < $this->len) {
                    //Next char is quote
                    if ($this->query[$pos + 1] == '\'') {
                        // Skip escape char and quotes
                        $pos++;
                        $string.='\'';
                        
                    } else {
                        // Otherwise include escape char
                        $string.=$c;
                    }
                    // If does not have next character
                } else {
                    // Exit look
                    break;
                }
                // If quota is found
            } else if ($c == '\'') {
                // Exit block
                break;
            } else {
                $string.=$c;
            }
        }

        $this->pos = $pos + 1;
        if ($this->pos < $this->len == false) {
            $this->pos = $this->len;
        }
        return $string;
    }
    
    protected function getDQuotedString() {
        $pos = $this->pos;
        $c = $this->query[$pos];
        $ord = ord($c);

        if ($ord != 34) {
            return null;
        } else {
            $pos++;
        }

        $string = "";
        for (; $pos < $this->len; $pos++) {
            $c = $this->query[$pos];
            $ord = ord($c);

            // If backslash is found
            if ($ord == 92) {
                // Has next character
                if (($pos + 1) < $this->len) {
                    //Next char is double quote
                    if ($this->query[$pos + 1] == '"') {
                        // Skip escape char and quotes
                        $pos++;
                        $string.='"';
                        
                    } else {
                        // Otherwise include escape char
                        $string.=$c;
                    }
                    // If does not have next character
                } else {
                    // Exit look
                    break;
                }
                // If double quotas are found
            } else if ($c == '"') {
                // Exit block
                break;
            } else {
                $string.=$c;
            }
        }

        $this->pos = $pos + 1;
        if ($this->pos < $this->len == false) {
            $this->pos = $this->len;
        }
        return $string;
    }
    
    protected function getNumber(){
        $pos = $this->pos;
        $c = "";
        $ord = 0;
        $string = "";
        $floating = false;
        
        
        for(; $pos < $this->len; $pos++){
            $c = $this->query[$pos];
            $ord = ord($c);
            
            // If this is a number
            if($ord > 47 && $ord < 58){
                $string.=$c;
                // Otherwise break the loop
            } else if($c=='-'&&($pos==$this->pos)){
                $string.=$c;
                // If c is first floating point separator, and this not a first char
            }else if($c=="."&&$floating==false&&($pos>$this->pos)){
                $string.=$c;
                $floating=true;
            }else {
                break;
            }
        }
        
        $this->pos = $pos;
        
        if($string!=="") {
            if($floating){
                return (float)$string;
            } else {
                return (int)$string;
            }
        } else {
            return null;
        }
    }

    
    protected function getWhitespace() {
        $pos = $this->pos;
        $c = "";
        $ord = 0;
        $string = "";
        $whiteChars = array(chr(0x09),chr(0x0A),chr(0x0B),chr(0x0C),
            chr(0x0D),chr(0x20));
        
        for(;$pos < $this->len; $pos++){
            $c = $this->query[$pos];
            $ord = ord($c);
            
            // If current char is a whitespace
            if(in_array($c, $whiteChars)){
                $string.=" ";
            } else {
                break;
            }
        }
        
        $this->pos = $pos;
        
        if($string){
            return true;
        } else {
            return null;
        }
    }
    
    
    
    /**
     * @return Azf_Service_Query_Tokenizer 
     */
    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        
        return self::$instance;
    }

}


