<?php

class SeleniumTestRunner extends PHPUnit_Extensions_SeleniumTestCase {
    
    protected $captureScreenshotOnFailure = false;
    protected $screenshotPath = '/var/www/azfcms-screenshots/';
    protected $screenshotUrl = 'http://azfcms-screenshots.poljana.vrw';
    
    public static $browsers = array(
        array(
            'name'=>"Newest Firefox",
            'browser'=>'*firefox'
        ),
        array(
            'name'=>'Newest Google Chrome',
            'browser'=>'*googlechrome'
        ),
        array(
            'name'=>'Newest Opera',
            'browser'=>'*opera'
        )
    );
    
    public function setUp(){
        $this->captureScreenshotOnFailure = true;
        $this->screenshotPath = getenv('screenshotLocalPath');
        $this->screenshotUrl = getenv('screenshotRemoteBasePath');
        $this->setBrowserUrl(getenv('apacheRemoteBasePath'));
        
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
