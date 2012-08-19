<?php

/**
 * 
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Application_Rest_User extends Azf_Rest_Provider_Abstract {

    public function isAllowed($request, $method, $id) {
        return Azf_Acl::hasAccess("resource.admin.rw");
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        return array(
            'id'=>$this->request->getId(),
            'firstName'=>"First Name",
            'lastName'=>"Last Name"
        );
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        return array(
            'id'=>$this->request->getId(),
            'firstName'=>"First Name",
            'lastName'=>"Last Name"
        );
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        
    }

}

