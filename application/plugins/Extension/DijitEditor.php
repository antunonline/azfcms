<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DijitEditor
 *
 * @author antun
 */
class Application_Plugin_Extension_DijitEditor extends Azf_Plugin_Extension_Abstract {

    public function render() {
        $params = $this->getParams();
        echo $params['body'];
    }

    public function setUp() {
        $this->setParam("body", "THIS IS VERY COOL:)");
    }

    public function tearDown() {
        
    }

}

