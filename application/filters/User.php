<?php

class Application_Filter_User extends Azf_Filter_Abstract {
    
    /**
     * name generic filter rule
     * @var array
     */
    protected $_nameGeneric = array(
        'filters' => array(
        ),
        'validators' => array(
            array('alnum', true),
            array('stringLength', 3, 40)
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
        'loginName' => array(
            'filters' => array(
            ),
            'validators' => array(
                array('alnum', true),
                array('stringLength', 3, 40),
                array('db_NoRecordExists', array(
                        'table' => 'User',
                        'field' => 'loginName'
                ))
            ),
            self::BRAKE_CHAIN_ON_FAILURE => true
        ),
        'firstName' => 'name',  // Points to name generic filter
        'lastName' => 'name',  // Points to name generic filter
        'email' => array(
            'filters' => array(),
            'validators' => array(
                'emailAddress'
            )
        ),
        'verified' => array(
            self::REQUIRED => false,
            self::ALLOW_EMPTY=>false
        ),
        'verificationKey' => array(
            self::REQUIRED => false,
            self::ALLOW_EMPTY=>true
        ),
        'password'=>array(
            'validators'=>array(
                array('stringLength',5)
            )
        )
    );

}
