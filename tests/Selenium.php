<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    public function setUp(){
        $this->setBrowser("firefox");
        $this->setBrowserUrl("file://".__DIR__);
        
    }
    
    public function testDoh(){
        $this->open("file://".__DIR__."/../public/js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        
        
        $this->assertTextPresent("0 errors");
        $this->assertTextPresent("0 failures");
        echo $this->getHtmlSource();
        
    }
    
    
}
