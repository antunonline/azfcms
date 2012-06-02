<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    
    public static $browsers = array(
        array(
            'name'=>"Newest Firefox",
            'browser'=>'*firefox'
        ),
        array(
            'name'=>'Newest Google Chrome',
            'browser'=>'*googlechrome',
            'host'=>'poljana.vrw'
        ),
        array(
            'name'=>'Newest Opera ',
            'browser'=>'*opera',
            'host'=>'poljana.vrw'
        )
    );
    
    public function setUp(){
        $this->setBrowserUrl(getenv('apacheRemoteBasePath'));
        
    }
    
    public function testDoh(){
        $this->open("public/js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        $log = $this->getText("id=logBody");
        $this->assertTextPresent("0 errors",$log);
        $this->assertTextPresent("0 failures",$log);
        echo $log;
        
    }
    
    
}
