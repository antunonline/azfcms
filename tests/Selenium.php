<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/azfcms-screenshots/';
    protected $screenshotUrl = 'http://azfcms-screenshots.poljana.vrw';
    
    public function setUp(){
        $this->captureScreenshotOnFailure = true;
        $this->screenshotPath = $GLOBALS['screenshotLocalPath'];
        $this->screenshotUrl = $GLOBALS['screenshotRemoteBasePath'];
        $this->setBrowser("firefox");
        $this->setBrowserUrl($GLOBALS['apacheRemoteBasePath']);
        
    }
    
    public function testDoh(){
        $this->open("public/js/lib/util/doh/runner.html?testModule=azfcms.tests.modules");
        $this->waitForTextPresent("TEST SUMMARY:");
        $log = $this->getText("id=logBody");
        $this->takeScreenshot();
        $this->assertTextPresent("0 errors",$log);
        $this->assertTextPresent("0 failures",$log);
        echo $log;
        
    }
    
    
}
