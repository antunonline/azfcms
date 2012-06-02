<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/azfcms-screenshots/';
    protected $screenshotUrl = 'http://azfcms-screenshots.poljana.vrw';
    
    public function setUp(){
        $this->setBrowser("firefox");
        $this->setBrowserUrl("http://azfcms.poljana.vrw");
        
    }
    
    public function testDoh(){
        $this->open("http://azfcms.poljana.vrw/public/js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        $log = $this->getText("id=logBody");
        $this->takeScreenshot();
        $this->assertTextPresent("0 errors",$log);
        $this->assertTextPresent("0 failures",$log);
        echo $log;
        
    }
    
    
}
