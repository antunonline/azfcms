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
class Application_Resolver_Doh extends Azf_Service_Lang_Resolver{
    protected function isAllowed($namespaces, $parameters) {
        return true;
    }
    
    public function uploadFilesMethod(){
        return $_FILES;
    }
}
