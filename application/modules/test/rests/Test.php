<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author antun
 */
class Test_Rest_Test extends Azf_Rest_Provider_Abstract{
    public function delete(\Azf_Rest_Request $request, \Azf_Rest_Response $response) {
        
    }

    public function get(\Azf_Rest_Request $request, \Azf_Rest_Response $response) {
        return $request->getId();
    }

    public function index(\Azf_Rest_Request $request, \Azf_Rest_Response $response) {
        
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(\Azf_Rest_Request $request, \Azf_Rest_Response $response) {
        
    }

    public function put(\Azf_Rest_Request $request, \Azf_Rest_Response $response) {
        
    }
}

