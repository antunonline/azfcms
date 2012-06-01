<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    public function setUp(){
        $this->setBrowser("firefox");
        $this->setBrowserUrl("http://azfcms.poljana.vrw");
        
    }
    
    public function testDoh(){
        $this->open("http://azfcms.poljana.vrw/public/js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        
        
        $this->assertTextPresent("0 errors");
        $this->assertTextPresent("0 failures");
        echo $this->getHtmlSource();
        
    }
    
    
}
