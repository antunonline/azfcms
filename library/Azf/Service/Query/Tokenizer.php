<?php

class Azf_Service_Query_Tokenizer {

    CONST T_STRING = "STRING";
    const T_WHITESPACE = "WHITESPACE";
    const T_NUMBER = "NUMBER";
    const T_QUOTED_STRING = "QUOTEDSTRING";

    protected $query;
    protected $pos = 0;
    protected $len = 0;

    /**
     *
     * @param string $query 
     * @return array
     */
    public function tokenize($query) {
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
        
        $return = $this->getNumber();
        if($return !== null){
            return array(self::T_NUMBER, $return);
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
        
        for(; $pos < $this->len; $pos++){
            $c = $this->query[$pos];
            $ord = ord($c);
            
            // If this is a number
            if($ord > 47 && $ord < 58){
                $string.=$c;
                // Otherwise break the loop
            } else {
                break;
            }
        }
        
        $this->pos = $pos;
        
        if($string!=="") {
            return $string;
        } else {
            return null;
        }
    }

    
    protected function getWhitespace() {
        $pos = $this->pos;
        $c = "";
        $ord = 0;
        $string = "";
        
    }

}

