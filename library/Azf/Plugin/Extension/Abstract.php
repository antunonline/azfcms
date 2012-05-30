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
abstract class Azf_Plugin_Extension_Abstract {
    
    /**
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * 
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }

    /**
     * 
     * @param array $params
     */
    public function setParams(array $params) {
        $this->_params = $params;
    }

    /**
     * 
     * @param array $params
     */
    function __construct(array $params=array()) {
        $this->setParams($params);
    }
    
    abstract public function setUp();
    abstract public function tearDown();
    abstract public function render();


}
