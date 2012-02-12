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

    abstract public function index();
    
    abstract public function get();
    
    abstract public function post();

    abstract public function put();
    
    abstract public function delete();
}