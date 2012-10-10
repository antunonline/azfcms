<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractBuilder
 *
 * @author antun
 */
class Azf_Tool_AbstractBuilder {

    protected $_log = array();

    public function log($msg) {
        $this->_log[] = $msg;
    }

    public function getLog() {
        return $this->_log;
    }

    public function getLogLines() {
        return implode("\n", $this->_log);
    }

    public function getLogLinesAndClean() {
        $return = implode("\n", $this->_log);
        $this->clearLog();
        return $return;
    }

    public function clearLog() {
        $this->_log = array();
    }

}