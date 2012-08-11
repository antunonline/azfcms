<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Factory
 *
 * @author antun
 */
class Azf_Filter_Factory {
    protected static $instance;
    
    protected $_loadedFilters = array();
    
    
    /**
     * 
     * @return Azf_Filter_Loader
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        self::$instance = $this;
    }
    
    
    protected function _constructFilterClass($filterName, $module){
        if($module=='default'){
            $module = "Application";
        }
        return ucfirst($module)."_Filter_".ucfirst($filterName);
    }
    
    
    protected function _getFilterKey($filterName, $module){
        return $module.$filterName;
    }
    
    protected function _isFilterLoaded($filterName, $module){
        $key = $this->_getFilterKey($filterName, $module);
        return isset($this->_loadedFilters[$key]);
    }
    
    /**
     * 
     * @param string $filterName
     * @param string $module
     * @return object
     */
    protected function _getLoadedFilter($filterName, $module){
        return $this->_loadedFilters[$this->_getFilterKey($filterName, $module)];
    }
    
    /**
     * 
     * @param string $filterName
     * @param string $module
     * @param object $filter
     */
    protected function _setLoadedFilter($filterName,$module,$filter){
        $this->_loadedFilters[$this->_getFilterKey($filterName, $module)] = $filter;
    }
    
    /**
     * 
     * @param string $filterName
     * @param string $module
     */
    protected function _loadFilter($filterName, $module){
        if($this->_isFilterLoaded($filterName, $module)){
            return $this->_getLoadedFilter($filterName,$module);
        } else {
            $className = $this->_constructFilterClass($filterName, $module);
            $filter = new $className();
            $this->_setLoadedFilter($filterName, $module, $filter);
            return $filter;
        }
    }
    


    /**
     * 
     * @param string $filterName
     * @param string $module
     */
    public function loadFilter($filterName, $module = "default"){
        return $this->_loadFilter($filterName,$module);
    }
    
    /**
     * 
     * @param string $filterName
     * @param string $module
     * @return Azf_Filter_Abstract
     */
    public static function get($filterName, $module='default'){
        return self::getInstance()->loadFilter($filterName,$module);
    }
}

