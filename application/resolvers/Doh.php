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
    
    
    public function getQueryLangStoreQueryRangeMethod($query=null, $options=array(), $staticOptions = array()){
        $dojoHelper = $this->getHelper("dojo");
        /* @var $dojoHelper Azf_Service_Lang_ResolverHelper_Dojo */
        
        $start = $dojoHelper->getQueryOptionsStart($options);
        $count = $dojoHelper->getQueryOptionsCount($options);
        
        $result = array();
        for($i = 0;$i<$count;$i++){
            $result[] = array(
                'id'=>$start+$i,
                'name'=>"Name ".($start+$i)
            );
        }
        
        return $dojoHelper->constructQueryResult($result,pow(10,8));
    }
}
