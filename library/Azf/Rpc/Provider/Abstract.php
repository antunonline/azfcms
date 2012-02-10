<?php

/**
 * Description of Abstract
 * // TODO 
 *
 * @author Antun Horvat <at> it-branch.com
 */
abstract class Azf_Rpc_Provider_Abstract {

    /**
     * This method is responsible for ACL initialization.
     * This method can be used to initialize components which are 
     * required to do the ACL check. This way we won't have to initialize 
     * everything and than throw it if the user does not have the
     * privilege to execute the method.
     */
    abstract protected function _initACL();

    /**
     *
     * @param string $methodName 
     * @return boolean
     */
    abstract protected function _isAllowed($methodName);

    /**
     * RPC provider initialization hook.
     * It will be called prior to method execution. 
     */
    abstract protected function _init();

    /**
     * Forward calls to protected method versions defined above. 
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed 
     */
    public function __call($name, $arguments) {
        return call_user_method_array("_" . $name, $this, $arguments);
    }

}
