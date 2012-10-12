<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author antun
 */
abstract class Azf_Plugin_Extension_Abstract{
    
    /**
     *
     * @var string
     */
    protected $_identifier;
    
    
    /**
     *
     * @var string
     */
    protected $_module;
    
    /**
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     *
     * @var boolean
     */
    protected $_isParamsDirty = false;
    
    /**
     *
     * @var int
     */
    protected $_id;
    
    /**
     * 
     * @return string
     */
    public function getIdentifier(){
        return $this->_identifier;
    }
    
    /**
     * 
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }

        
    /**
     * 
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }
    
    
    /**
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed 
     */
    public function getParam($key, $default = null){
        if(isset($this->_params[$key])){
            return $this->_params[$key];
        } else {
            return $default;
        }
    }

    /**
     * 
     * @param array $params
     */
    public function setParams(array $params) {
        $this->_params = $params;
        $this->_isParamsDirty=true;
    }
    
    
    /**
     * 
     * @param mixed $name
     * @param mixed $value
     */
    public function setParam($name,$value){
        $this->_params[$name]=$value;
        $this->_isParamsDirty=true;
    }
    
    public function isParamsDirty() {
        return $this->_isParamsDirty;
    }
    
    public function clearParamsDirty(){
        $this->_isParamsDirty=false;
    }
    
    /**
     * 
     * @return int|null
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * 
     * @param int $id
     */
    public function setId($id) {
        $this->_id = $id;
    }

    
    /**
     * 
     * @param array $params
     */
    function __construct($identifier, $module, array $params=array()) {
        $this->_identifier = $identifier;
        $this->_module = $module;
        $this->_params = $params;
    }
    
    
    /**
     *
     * @param int|string $key
     * @param mixed $value 
     */
    public function setValue($key,$value){
        $this->setParam($key, $value);
    }
    
    
    /**
     *
     * @param array|object $values 
     */
    public function setValues($values){
        $this->setParams((array) $values);
    }
    
    /**
     *
     * @param int|string$key
     * @return mixed
     */
    public function getValue($key) {
        return $this->getParam($key);
    }
    
    /**
     *
     * @return array
     */
    public function getValues(){
        return $this->getParams();
    }
    
    
    /**
     * 
     * @param string $template
     * @return string
     */
    protected function renderTemplate($template){
        if($this->getModule()=="default"){
            $templatePath = APPLICATION_PATH."/views/pluginScripts/".$this->getIdentifier()."/".$template.".phtml";
        } else {
            $templatePath = APPLICATION_PATH."/modules/".$this->getModule()."/views/pluginScripts/".$this->getIdentifier()."/".$template.".phtml";
        }
        
        return include $templatePath;
    }


    abstract public function setUp();
    abstract public function tearDown();
    abstract public function render();


}
