<?php

/**
 * Description of ProviderA
 * // TODO
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Application_Rpc_ProviderA extends Azf_Rpc_Provider_Abstract {

    /**
     *
     * @param int $a
     * @param int $b 
     * @return int
     */
    public function add($a, $b) {
        return $a + $b;
    }

    protected function _init() {
        
    }

    protected function _initACL() {
        
    }

    protected function _isAllowed($methodName) {
        return true;
    }

}
