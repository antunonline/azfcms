<?php
/**
 * Description of Bootstrap
 *
 * @author antun
 */
class Test_Bootstrap extends Zend_Application_Module_Bootstrap{
    public function _initLoader() {
        $loader = new Zend_Application_Module_Autoloader(array(
            'basePath'=>__DIR__,
            'namespace'=>"Test"
        ));
        $loader->addResourceType("rests", "rests", "Rest");
    }
}
