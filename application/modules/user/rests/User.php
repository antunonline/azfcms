<?php

class User_Rest_User extends Azf_Rest_Provider_Abstract {

    protected $_requestFrom = 0;
    protected $_requestCount = 20;
    protected $_responseFrom = 0;
    protected $_responseCount = 0;
    protected $_responseAvailable = 0;

    public function getRequestFrom() {
        return $this->_requestFrom;
    }

    public function getRequestCount() {
        return $this->_requestCount;
    }

    public function getResponseFrom() {
        return $this->_responseFrom;
    }

    public function setResponseFrom($responseFrom) {
        $this->_responseFrom = $responseFrom;
    }

    public function getResponseCount() {
        return $this->_responseCount;
    }

    public function setResponseCount($responseCount) {
        $this->_responseCount = $responseCount;
    }

    public function getResponseAvailable() {
        return $this->_responseAvailable;
    }

    public function setResponseAvailable($responseAvailable) {
        $this->_responseAvailable = $responseAvailable;
    }

    protected function _initRangeHeader() {
        if (isset($_SERVER['HTTP_RANGE'])) {
            $header = str_replace("items=", "", $_SERVER['HTTP_RANGE']);
            $chunks = explode("-", $header);
            if (count($chunks) == 2) {
                $from = $chunks[0];
                $to= $chunks[1];
                $count = $to-$from;
                if (ctype_digit($from) && ctype_digit($count)) {
                    return;
                } else if ($from < 0 || $count > 150) {
                    return;
                } else {
                    $this->_requestFrom = $from;
                    $this->_requestCount = $count;
                }
            }
        }
    }

    public function init() {
        parent::init();
        $this->_initRangeHeader();
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $this->setContentRange($this->getRequestFrom(),$this->getRequestCount(),10000);
        return array_fill(0, $this->getRequestCount(), array('firstName' => "Test", 'lastName' => 'test', 'id' => 11));
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function setContentRange($from, $count, $available) {
        $to = $from+$count;
        header("Content-Range: $from-$to/$available");
    }

}