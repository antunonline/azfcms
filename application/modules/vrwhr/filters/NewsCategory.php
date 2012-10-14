<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewsCategory
 *
 * @author antun
 */
class Vrwhr_Filter_NewsCategory extends Azf_Filter_Abstract {
    
    /**
     * Name generic
     * 
     * If you want to use it in your filter definition, just
     * point field to generic name
     * 
     * Example:
     * 'title'=>'name'
     * @var array
     */
    protected $_nameGeneric = array(
        'filters' => array(
        ),
        'validators' => array(
            array('alnum', true),
            array('stringLength', 2, 40)
        ),
        self::BRAKE_CHAIN_ON_FAILURE => true
    );
    
    /**
     *
     * @var array
     */
    protected $_filterRules = array(
        'id' => array(
            'filters' => array(
                'digits'
            ),
            'validators' => array(
                'digits'
            ),
            self::BRAKE_CHAIN_ON_FAILURE => true
        ),
        'title'=>'name',
        'description' => array(
            'filters' => array(
                'stringTrim'
            ),
            'validators' => array(
                array('stringLength', 0, 255)
            ),
        )
    );
}
