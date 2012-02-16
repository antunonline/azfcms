<?php

class Azf_Rest_Request {

    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    /**
     *
     * @var array
     */
    protected $_request;

    /**
     *
     * @var array
     */
    protected $_queryArgs = array();

    /**
     *
     * @var array
     */
    protected $_parsedArgs = array();

    /**
     *
     * @var mixed
     */
    protected $_parsedBody = null;

    /**
     *
     * @var boolean
     */
    protected $_isValid = false;

    /**
     *
     * @return string
     */
    public function getMethod() {
        return $this->_request['REQUEST_METHOD'];
    }

    /**
     * @return string 
     */
    public function getModuleName() {
        return $this->_parsedArgs['module'];
    }

    /**
     * @return string 
     */
    public function getProviderName() {
        return $this->_parsedArgs['provider'];
    }

    /**
     * @return string 
     */
    public function getId() {
        return $this->_parsedArgs['id'];
    }

    public function getBody() {
        return $this->_parsedBody;
    }

    /**
     *
     * @return boolean
     */
    public function isValid() {
        return $this->_isValid;
    }

    /**
     *
     * @param string $name
     * @param mixed $default
     * @return string
     */
    public function getQueryArg($name, $default = null) {
        if (isset($this->_queryArgs[$name])) {
            return $this->_queryArgs[$name];
        } else {
            return $default;
        }
    }

    /**
     *
     * @param string $name
     * @param mixed $default
     * @return string
     */
    public function getRequestArg($name, $default = null) {
        if (isset($this->_request[$name])) {
            return $this->_request[$name];
        } else {
            return $default;
        }
    }

    /**
     * 
     * @param array $request  Request object
     */
    public function __construct(array $request = array(), array $queryArgs = array()) {
        $this->_request = $request;
        $this->_queryArgs = $queryArgs;
        try {
            $this->_init();
            $this->_isValid = true;
        } catch (Exception $e) {
            if (Zend_Registry::isRegistered("logLevel") ? Zend_Registry::get("logLevel") : 0 & E_ALL == E_ALL) {
                Zend_Registry::get("log")->log($e->getMessage(), Zend_Log::DEBUG);
            }
        }
    }

    protected function _init() {
        $this->_initRequestEnv();
        $this->_parseArgs();
        $this->_parseBody();
    }

    /**
     * Initialize request object 
     */
    protected function _initRequestEnv() {
        if (!$this->_request) {
            $this->_request = $_SERVER;
        }
        if (!$this->_queryArgs) {
            $this->_queryArgs = $_GET;
        }
    }

    protected function _parseArgs() {
        $url = $this->_request['REQUEST_URI'];
        // Remove query part
        if (false !== ($pos = strpos($url, "?"))) {
            $url = substr($url, 0, $pos);
            unset($pos);
        }
        // Remove URL prefix
        $url = str_replace("/json-rest.php/", "", $url);
        $url = rtrim($url, "/");

        $parsedArgs = array();

        $parts = array_slice(explode("/", $url), 0, 3);

        if (count($parts) >= 2) {
            foreach ($parts as $key => $value) {
                if (!$value || (!ctype_alpha($value[0]) && $key != 2) || !ctype_alnum($value)) {
                    throw new RuntimeException("Provided URL ($url) is not properly formatted.");
                }
                switch ($key) {
                    case 0:
                        $parsedArgs['module'] = $value == "default" ? "application" : $value;
                        break;
                    case 1:
                        $parsedArgs['provider'] = $value;
                        break;
                    case 2:
                        $parsedArgs['id'] = $value;
                        break;
                }
            }
        } else {
            throw new RuntimeException("Provided URL ($url) is not properly formatted.");
        }


        $this->_parsedArgs = $parsedArgs + array('id' => '');
    }

    protected function _parseBody() {
        $content = file_get_contents("php://input");
        $this->_parsedBody = json_decode($content);
    }

}
