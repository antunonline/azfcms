<?php

class Application_Filter_AclGroup extends Azf_Filter_Abstract {

    /**
     *
     * @var array
     */
    protected $_nameGeneric = array(
        'filters' => array(
        ),
        'validators' => array(
            array('alnum', true),
            array('stringLength', 2, 40),
            array('db_NoRecordExists', array(
                        'table' => 'ACLGroup',
                        'field' => 'name'
                ))
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
        'name'=>'name',
        'description'=>array(
            'filters'=>array(
                'stringTrim'
            ),
            'validators'=>array(
                array('alnum',true),
                array('stringLength',0,255)
            ),
            
        )
    );

}
