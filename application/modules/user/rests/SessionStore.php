<?php

class User_Rest_SessionStore extends Azf_Rest_Provider_DojoStore {

    /**
     *
     * @var array
     */
    protected $records = null;

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $session = null;

    protected function _initRecords(Zend_Session_Namespace $session) {
        $records = array();
        for ($i = 1; $i <= 1000; $i++) {
            $records[$i] = array(
                'id' => $i,
                'firstName' => "First " . $i,
                'lastName' => "Last " . $i
            );
        }
        $session->records = $records;
    }

    public function init() {
        parent::init();
        $session = new Zend_Session_Namespace("sessionStore");
        if (isset($session->records) == false) {
            $this->_initRecords($session);
        }
        $this->records = $session->records;
        $this->session = $session;
    }

    public function _uninit() {
        $this->session->records = $this->records;
    }

    public function delete(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        unset($this->records[$this->getRequest()->getId()]);
        return true;
    }

    public function get(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        return $this->records[$request->getId()];
    }

    public function index(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        // Set returned result set content range
        $this->setContentRange(count($this->records));
        // Return request part
        return array_slice($this->records, $this->requestFrom, $this->requestCount);
    }

    public function isAllowed($request, $method, $id) {
        return true;
    }

    public function post(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $requestBody = $request->getBody();
        $firstName = $requestBody->firstName;
        $id = $request->getId();
        $lastName = $requestBody->lastName;

        return $this->records[(int)$id] = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'id' => $id
        );
    }

    public function put(Azf_Rest_Request $request, Azf_Rest_Response $response) {
        $requestBody = $request->getBody();
        $firstName = $requestBody->firstName;
        $lastName = $requestBody->lastName;

        return $this->records[(int)$request->getId()] = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'id' => $request->getId()
        );
    }

}