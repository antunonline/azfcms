<?php

class User_Rest_User extends Azf_Rest_Provider_DojoStore {

    public function getSortableFields() {
        return array('firstName', 'lastName');
    }

    public function getFilterableFields() {
        return array(
            'firstName','lastName','id'
        );
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $this->setContentRange($this->requestFrom, $this->requestCount, 10000);

        $filter = "";
        foreach ($this->filterFields as $def) {
            $filter.= " ". implode("-", $def);
        }
        return array_fill(0, $this->getRequestCount(), array('firstName' => "Test " . implode("-", array_keys($this->sortFields)), 'lastName' => '' . $filter, 'id' => 11));
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}