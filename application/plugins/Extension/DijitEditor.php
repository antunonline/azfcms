<?php

/**
 * Description of DijitEditor
 *
 * @author antun
 */
class Application_Plugin_Extension_DijitEditor extends Azf_Plugin_Extension_Abstract {

    public function render() {
        $this->renderTemplate("index");
    }

    public function setUp() {
        $this->setParam("body", "");
    }

    public function tearDown() {
        
    }

}

