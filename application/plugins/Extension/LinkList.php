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
class Application_Plugin_Extension_LinkList extends Azf_Plugin_Extension_Abstract {

    public function render() {
        $this->renderTemplate("linkList");
    }

    public function setUp() {
        $this->setParams(array(
            'linkList'=>array(),
            'title'=>""
        ));
    }

    public function tearDown() {
        
    }
    
    protected function renderTemplate($template) {
        
        /**
         * Use $this->getParam("linkList"); to get a list of links
         * that we need to render
         */
        parent::renderTemplate($template);
    }

}

