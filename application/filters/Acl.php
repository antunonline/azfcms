<?php

class Application_Filter_Acl extends Azf_Filter_Abstract {

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
        'resource' => array(
            'filters' => array(
                'stringTrim'
            ),
            'validators' => array(
                array('regex', 'pattern'=>'/^[a-z0-9](.[a-z0-9]+)*$/')
            )
        ),
        'desription' => array(
            'filters' => array(
                'stringTrim'
            ),
            'validators' => array(
                array('alnum', true),
                array('stringLength', 0, 255)
            ),
        )
    );

}
