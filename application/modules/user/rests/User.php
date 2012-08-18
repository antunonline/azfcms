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
        $this->setContentRange(10000, $this->requestFrom, $this->requestCount);

        $filter = "";
        foreach ($this->filterFields as $def) {
            $filter.= " ". implode("-", $def);
        }
        
        $response = array();
        for($i = $this->requestFrom; $i < $this->requestFrom+$this->requestCount; $i++){
            $response[] = array(
              'id'=>$i,
                'firstName'=>$filter,
                'lastName'=>implode(',',$this->sortFields)
            );
        }
        return $response;
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}