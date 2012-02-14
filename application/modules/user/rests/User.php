<?php

class User_Rest_User extends Azf_Rest_Provider_DojoStore {

    protected function _initRangeHeader() {
        if (isset($_SERVER['HTTP_RANGE'])) {
            $header = str_replace("items=", "", $_SERVER['HTTP_RANGE']);
            $chunks = explode("-", $header);
            if (count($chunks) == 2) {
                $from = $chunks[0];
                $to = $chunks[1];
                $count = $to - $from;
                if (ctype_digit($from) && ctype_digit($count)) {
                    return;
                } else if ($from < 0 || $count > 150) {
                    return;
                } else {
                    $this->requestFrom = $from;
                    $this->requestCount = $count;
                }
            }
        }
    }

    public function getSortableFields() {
        return array ('firstName','lastName');
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $this->setContentRange($this->requestFrom, $this->requestCount, 10000);
        return array_fill(0, $this->getRequestCount(), array('firstName' => "Test " . implode("-", array_keys($this->sortFields)), 'lastName' => 'test', 'id' => 11));
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}