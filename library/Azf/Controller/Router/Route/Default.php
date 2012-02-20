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
     * @var ITBranch_Controller_Router_Route_Default
     */
    protected static $instance;
    
    /**
     *
     * @var Azf_Navigation_NavigationTree
     */
    protected $model;

    /**
     *
     * @return Azf_Navigation_NavigationTree
     */
    public function getModel() {
        return $this->model;
    }

    /**
     *
     * @param Azf_Navigation_NavigationTree $model 
     */
    public function setModel(Azf_Navigation_NavigationTree $model) {
        $this->model = $model;
    }

    /**
     *
     * @param Azf_Navigation_NavigationTree $model 
     */
    public function __construct(Azf_Navigation_NavigationTree $model) {
        $this->setModel($model);
    }

    public function assemble($data = array(), $reset = false, $encode = false) {
        $url = "";
        if(!isset($data['id'])){
            return false;
        }
        else if(isset($data['id'])&&isset($data['name'])){
            $node['id']=$data['id'];
            $node['name']=$data['name'];
        }
        else {
            $id = $data['id'];
            $node = $this->getModel()->find($id);
            
            if(!$node){
                return false;
            }
        }
        
        $params= array_diff_key($data,$node);        
        
        $url.=urlencode(preg_replace("/._d(\d)/",".-d $1",$node['name'])."._d").$node['id']."/";
        
        foreach($params as $key => $value){
            if(is_string($value)==false) continue;
            $url.=urlencode($key)."/".urlencode($value)."/";
        }
        
        return $url;
    }

    public function match($path) {
        /* @var $regex Zend_Controller_Request_Http */
        $regex = '/[^.]+\._d(\d+)/i';
        $matches = array();
        $match = array();
        $requestUri = $path->getRequestUri();
        
        if(preg_match($regex,$requestUri, $matches)){
            $node = $this->getModel()->find($matches[1]);
            if($node){
                $match =(array) json_decode($node['body']);
                $match['id']=$node['id'];
                
                $matchString = "._d".$node['id']."/";
                if(strpos($requestUri, $matchString)!== -1){
                    $requestUri = substr($requestUri,strpos($requestUri,$matchString)+strlen($matchString));
                    $lPos = 0;
                    $pos = 0;
                    $key = false;
                    $value = false;
                    
                    while(false !== ($pos = strpos($requestUri,"/",$lPos))){
                        
                        if($key === false){
                            $key = substr($requestUri, $lPos,$pos-$lPos);
                        }
                        else {
                            $value = substr($requestUri, $lPos,$pos-$lPos);
                            $match[urldecode($key)]=urldecode($value);
                            
                            $key = false;
                            $value = false;
                        }
                        
                        $lPos = $pos+1;
                    }
                    if(($key && !$value) || ($lPos<strlen($requestUri))){
                        if($lPos<strlen($requestUri)){
                            if($key){
                                $value = substr($requestUri, $lPos);
                            }
                            else {
                                $key = substr($requestUri, $lPos);
                                $value = "";
                            }
                        }
                        else {
                            $value = "";
                        }
                        $match[urldecode($key)]=urldecode($value);
                    }
                }
                
            }
        }
        
        if(!$match){
            $node = $this->getModel()->getRootNode("*",true);
            $match = json_decode($node['body']);
        }
        return $match;
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
