<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Navigation
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_Tree_Navigation extends Azf_Model_Tree_Abstract {

    protected function createTemporaryTable() {
        $sql = <<<SQL
CREATE  TEMPORARY TABLE IF NOT EXISTS `TemporaryNavigation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parentId` INT UNSIGNED NULL ,
  `tid` INT UNSIGNED NOT NULL ,
  `l` INT UNSIGNED NULL ,
  `r` INT UNSIGNED NULL ,
  `disabled` INT UNSIGNED NOT NULL DEFAULT 1 ,
  `url` VARCHAR(125) NULL ,
  `final` MEDIUMTEXT NOT NULL ,
  `plugins` MEDIUMTEXT NOT NULL ,
  `abstract` MEDIUMTEXT NOT NULL);
SQL;
        $this->getAdapter()->query($sql);
    }

    protected function dropTemporaryTable() {
        $sql = "DROP TABLE TemporaryNavigation;";
        $this->getAdapter()->query($sql);
    }

    protected function insertNode($l, $r, $parentId, $value) {
        $value = (object) $value;
        $record = array(
            'l' => $l,
            'r' => $r,
            'parentId' => $parentId,
            'tid' => $this->tid,
            'disabled' => $value->disabled,
            'url' => $value->url,
            'final' => $value->final,
            'plugins' => $value->plugins,
            'abstract' => $value->abstract
        );

        return Zend_Db_Table_Abstract::insert($record);
    }

    protected function mergeTemporaryTable() {
        $sql = <<<SQL
INSERT INTO Navigation SELECT * FROM TemporaryNavigation;   
SQL;
        $this->getAdapter()->query($sql);
    }

    protected function moveNodeIntoTemporaryTable($node) {
        $sql = <<<SQL
        INSERT INTO TemporaryNavigation SELECT * FROM Navigation n WHERE n.l >= ? AND n.r <= ?;
SQL;
        $this->getAdapter()->query($sql, array($node['l'], $node['r']));
    }

    protected function parseNode(array $r) {
        $r['childNodes'] = array();
        unset($r['tid']);

        return $r;
    }

    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?", array($id));
    }
    
    
    

}

