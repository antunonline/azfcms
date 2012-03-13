<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Default
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Controller_Router_Route_Default extends Zend_Controller_Router_Route_Abstract {

    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $model = null;

    /**
     *
     * @var int
     */
    protected $matchId = -1;

    /**
     * @var string
     */
    protected $url = "";

    /**
     *
     * @var array
     */
    protected $matchQuery = array();

    /**
     *
     * @return int
     */
    public function getMatchedId() {
        return $this->matchId;
    }

    /**
     *
     * @param int $matchId 
     */
    public function setMatchedId($matchId) {
        $this->matchId = $matchId;
    }

    /**
     *
     * @return string
     */
    public function getMatchedUrl() {
        return $this->url;
    }

    /**
     *
     * @param string $url 
     */
    public function setMatchedUrl($url) {
        $this->url = $url;
    }

    /**
     *
     * @return array
     */
    public function getMatchedQueryParams() {
        return $this->matchQuery;
    }

    /**
     * @param array $matchQuery
     */
    public function setMatchedQueryParams(array $matchQuery) {
        $this->matchQuery = $matchQuery;
    }

    /**
     *
     * @param Azf_Model_Tree_Navigation $model 
     */
    public function __construct(Azf_Model_Tree_Navigation $model) {
        $this->model = $model;
    }

    /**
     *
     * @param array $data
     * @param boolean $reset
     * @param boolean $encode
     * @return string 
     */
    public function assemble($data = array(), $reset = false, $encode = false) {
        $id = isset($data['id']) ? $data['id'] : $this->getMatchedId();
        $url = isset($data['url']) ? $data['url'] : $this->getMatchedUrl();
        
        if($reset==false){
            $data += $this->getMatchedQueryParams();
        }

        unset($data['id']);
        unset($data['url']);
        $url = urlencode($url);
        $queryArgs = http_build_query($data);
        $assembled = "/";


        if ($url)
            $assembled .=$url . "/";
        $assembled .="$id.html";

        if ($queryArgs)
            $assembled.="?$queryArgs";

        return $assembled;
    }

    /**
     *
     * @param string $path
     * @return array
     */
    public function match($path) {
        if(is_object($path) && $path instanceof Zend_Controller_Request_Http){
            $path = $path->getRequestUri();
        }
        $urlParts = parse_url($path);
        $urlPath = isset($urlParts['path']) ? $urlParts['path'] : "";
        $urlQuery = isset($urlParts['query']) ? $urlParts['query'] : "";
        $id = substr($urlPath, ($lPos = 1 + strrpos($urlPath, "/")), strrpos($urlPath, ".html") - $lPos);
        $id = ctype_digit($id) ? $id : -1;
        $queryParams = array();
        parse_str($urlQuery, $queryParams);

        $id = $this->model->match($id);
        $staticParams = array("id"=>$id) + (array) $this->model->getStaticParams($id);
        
        

        $this->setMatchedId($id);
        $this->setMatchedUrl($urlPath);
        $this->setMatchedQueryParams($queryParams);

        return $staticParams;
    }

    /**
     *
     * @param Zend_Config $config
     * @return ITBranch_Controller_Router_Route_Default
     */
    public static function getInstance(Zend_Config $config) {
        return self::$instance;
    }
    

}
