<?php

/**
 * Description of Server
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Rest_Server {

    /**
     *
     * @var Azf_Rest_Request
     */
    protected $request = null;

    /**
     *
     * @var Azf_Rest_Response
     */
    protected $response = null;

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

    /**
     *
     * @return Azf_Rest_Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     *
     * @param Azf_Rest_Response $response 
     */
    public function setResponse(Azf_Rest_Response $response) {
        $this->response = $response;
    }

    public function __construct() {
        ;
    }

    /**
     *  
     */
    protected function _init() {
        $this->_initRequest();
        $this->_initResponse();
        $this->_initModule();
    }

    /**
     * Initialize request object
     */
    protected function _initRequest() {
        $this->setRequest(new Azf_Rest_Request());
        // If request is not properly initialized
        if($this->getRequest()->isValid() == false){
            throw new RuntimeException("Request object could not be properly initialized");
        }
    }

    protected function _initResponse() {
        $this->setResponse(new Azf_Rest_Response());
    }

    protected function _initModule() {
        // TOOD
    }

    public function handle() {
        try {
            $this->_init();
        } catch (Exception $e) {
            echo $e->getMessage();
            if (Zend_Registry::isRegistered("logLevel")?Zend_Registry::get("logLevel") : 0 & E_ALL == E_ALL) {
                Zend_Registry::get("log")->log($e->getMessage(), Zend_Log::DEBUG);
            }
            return;
        }
    }

}

