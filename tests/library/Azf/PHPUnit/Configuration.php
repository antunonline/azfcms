<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Configuration
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_PHPUnit_Configuration {
    /**
     *
     * @var array
     */
    protected static $config;
    
    protected static function _init(){
        if(self::$config){
            return;
        }
        self::$config = new Zend_Config_Ini("./configuration.ini");
        self::$config = self::$config->toArray();
    }
    
    
    /**
     *
     * @param string $name
     * @return mixed 
     */
    public static function get($name){
        self::_init();
        return self::$config[$name];
    }
}
