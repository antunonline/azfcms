<?php

class Azf_Service_Query_Descriptor {
    
    const TYPE_PROPERTY = "PROPERTY";
    const TYPE_METHOD = "METHOD";
    /**
     * Namespace of the object identifier. Namespace will  link resolver and
     * identifier. If we do not specify namespace object first sucessfull resolved object
     * will be used as identified object. 
     * @var string
     */
    protected $namespace;
    /**
     *
     * @var String 
     */
    protected $identifier;
    /**
     *
     * @var array
     */
    protected $arguments;
    
    /**
     *  Type of the identifiers. There are two acceptable identifier types: 
     * property and method
     *
     * @var boolean
     */
    protected $type;
    
    
    
}
