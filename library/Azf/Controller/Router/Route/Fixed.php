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
class Azf_Controller_Router_Route_Fixed extends Zend_Controller_Router_Route_Abstract {

    /**
     *
     * @var string
     */
    protected $module;

    /**
     *
     * @var string
     */
    protected $controller;

    /**
     *
     * @var string
     */
    protected $action;

    /**
     *
     * @var int
     */
    protected $id;
    
    
    /**
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param string $id 
     */
    function __construct($module=null, $controller=null, $action=null, $id=null) {
        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        $this->id = $id;
    }

        public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function getController() {
        return $this->controller;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setMvcParams(array $params){
        $p = (object)$params;
        $this->setId($p->id);
        $this->setModule($p->module);
        $this->setController($p->controller);
        $this->setAction($p->action);
    }

    /**
     *
     * @param array $data
     * @param boolean $reset
     * @param boolean $encode 
     */
    public function assemble($data = array(), $reset = false, $encode = false) {
        return "";
    }

    
    /**
     * @return array
     */
    public function match($path) {
        return array(
            "module"=>$this->getModule(),
            'controller'=>$this->getController(),
            'action'=>$this->getAction(),
            'id'=>$this->getId()
        );
    }

    public static function getInstance(Zend_Config $config) {
        
    }

}
