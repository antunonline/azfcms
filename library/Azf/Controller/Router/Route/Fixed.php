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
    
    function __construct(array $params = array()) {
        $this->_params = $params;
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
        return $this->getParams();
    }

    public static function getInstance(Zend_Config $config) {
        
    }

}
