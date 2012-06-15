<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    
    public static $browsers = array(
        array(
            'name'=>"Newest Firefox",
            'browser'=>'*firefox',
            'timeout'=>240
        ),
        array(
            'name'=>'Newest Google Chrome',
            'browser'=>'*googlechrome',
            'host'=>'poljana.vrw',
            'timeout'=>240
        ),
        array(
            'name'=>'Newest Opera ',
            'browser'=>'*opera',
            'host'=>'poljana.vrw',
            'timeout'=>240
        )
    );
    
    public function setUp(){
        $this->setBrowserUrl(getenv('apacheRemoteBasePath'));
        
    }
    
    public function testDoh(){
        $this->open("js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        $log = $this->getText("id=logBody");
        $this->assertTextPresent("0 errors",$log);
        $this->assertTextPresent("0 failures",$log);
        echo $log;
        
    }
    
    
}
