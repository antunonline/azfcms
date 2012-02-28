<?php

/**
 * Description of Navigation
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Navigation extends Azf_Model_Tree_Abstract {
    
    protected $_name = "Navigation";
    protected $_primary = "id";

    protected function createTemporaryTable() {
        $sql = "CREATE TEMPORARY TABLE TemporaryNavigationTable ("
        ."id INT UNSIGNED NOT NULL,"
        ."parentId INT UNSIGNED NOT NULL,"
        ."tid INT UNSIGNED NOT NULL,"
        ."l INT UNSIGNED NOT NULL,"
        ."r INT UNSIGNED NOT NULL,"
        ."`final` MEDIUMTEXT NOT NULL,"
        ."`abstract` MEDIUMTEXT NOT NULL,"
        ."`plugins` MEDIUMTEXT NOT NULL,"
        ."`disabled` TINYINT NOT NULL DEFAULT 1,"
        ."`acl` SMALLINT NOT NULL DEFAULT 0,"
        .");"
        ;
        
        $this->getAdapter()->query($sql);
        
    }

    protected function dropTemporaryTable() {
        $sql = "DROP TABLE TemporaryNavigationTable;";
        
        $this->getAdapter()->query($sql);
    }

    protected function insertNode($l, $r, $parentId, $value) {
        $record = array(
            'l'=>$l,
            'r'=>$r,
            'parentId'=>$parentId,
            'final'=>$value['final'],
            'abstract'=>$value['abstract'],
            'plugins'=>$value['plugins']
        );
        Zend_Db_Table_Abstract::insert($record);
    }

    protected function mergeTemporaryTable() {
        
    }

    protected function moveNodeIntoTemporaryTable($node) {
        
    }

    protected function parseNode(array $r) {
        
    }

    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * from Navigation WHERE id = ?",array($id));
    }

}

