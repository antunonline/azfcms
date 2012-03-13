<?php

/**
 * Description of ReturnSequential
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_PHPUnit_Framework_MockObject_Stub_ReturnSequential implements PHPUnit_Framework_MockObject_Stub {

    /**
     *
     * @var int
     */
    protected $i = 0;

    /**
     *
     * @var array
     */
    protected $values = array();

    public function __construct(array $values) {
        $this->values = $values;
    }

    //put your code here
    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation) {
        return $this->values[$this->i++];
    }

    public function toString() {
        return sprintf(
                        'return user-specified value %s', PHPUnit_Util_Type::toString($this->value)
        );
    }

}
