<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Doh
 *
 * @author antun
 */
class Application_Resolver_Acl extends Azf_Service_Lang_Resolver {
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
}
