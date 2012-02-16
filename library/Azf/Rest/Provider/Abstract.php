<?php

/**
 * @author Antun Horvat 
 */
abstract class Azf_Rest_Provider_Abstract {

    /**
     *
     * @var Azf_Rest_Request;
     */
    protected $request;

    /**
     *
     * @return Azf_Rest_Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     *
     * @param Azf_Rest_Request $request 
     */
    public function setRequest(Azf_Rest_Request $request) {
        $this->request = $request;
    }

    public function init() {
        
    }
    
    public function _uninit(){
        
    }
    
    
    /**
     * @param Azf_Rest_Request $request
     * @param string $method
     * @param string $id
     * @return boolean 
     */
    abstract public function isAllowed($request, $method, $id);

    abstract public function index(Azf_Rest_Request $request, Azf_Rest_Response $response);

    abstract public function get(Azf_Rest_Request $request, Azf_Rest_Response $response);

    abstract public function post(Azf_Rest_Request $request, Azf_Rest_Response $response);

    abstract public function put(Azf_Rest_Request $request, Azf_Rest_Response $response);

    abstract public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response);
}