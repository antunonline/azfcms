<?php

class Azf_Rest_Response {

    const HTTP_OK = "200";
    const HTTP_CREATED = "201";
    const HTTP_NO_CONDENT = "204";
    const HTTP_PARTIAL_CONTENT = "206";
    const HTTP_MULTIPLE_CHOICES = "300";
    const HTTP_MOVED_PERNAMENTLY = "301";
    const HTTP_BAD_REQUEST = "400";
    const HTTP_UNAUTHORIZED = "401";
    const HTTP_FORBIDDEN = "403";
    const HTTP_UNSUPPORTED_MEDIA_TYPE = "415";
    const HTTP_INTERNAL_SERVER_ERROR = "500";

    /**
     *
     * @var string
     */
    protected $body = "";

    /**
     *
     * @var type 
     */
    protected $responseCode = "";

    /**
     *
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     *
     * @param string $body 
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     *
     * @param string $body 
     */
    public function addBody($body) {
        $this->body.=$body;
    }

    /**
     *
     * @return string
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     *
     * @param string $response 
     */
    public function setResponseCode($response) {
        $this->responseCode = $response;
    }
    
    
    public function doResponse(){
        header("",true,$this->getResponseCode());
        echo $this->getBody();
    }

}
