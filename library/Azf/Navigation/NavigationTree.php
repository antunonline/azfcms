<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NavigationTree
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Navigation_NavigationTree extends Azf_Navigation_AbstractTreeModel{
    protected $_name = "Navigation";
    protected $_primary = "id";

    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?", array($id));
    }

    protected function insertNode($l, $r, $parentId, $value) {
        return $this->insert(array(
                    'l' => $l,
                    'r' => $r,
                    'parentId' => $parentId,
                    'tid' => $this->tid,
                    'name' => $value['name'],
                    'name,body' => $value['body']
                ));
    }
    
    protected function createTemporaryTable(){
        $temporaryTreeTableDDLSQL = "CREATE TEMPORARY TABLE TemporaryTreeTable( id INT UNSIGNED NOT NULL, parentId INT UNSIGNED, l INT UNSIGNED, r INT UNSIGNED, tid INT UNSIGNED, name VARCHAR(255), body MEDIUMTEXT );";
        $this->getAdapter()->query($temporaryTreeTableDDLSQL);
    }
    
    protected function moveNodeIntoTemporaryTable($node) {

        $moveNodeIntoTemporaryTableSQL = "INSERT INTO TemporaryTreeTable (id, parentId, l, r, tid, name, body) SELECT id, parentId, l, r, tid, name, body FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($moveNodeIntoTemporaryTableSQL, array($node['l'], $node['r'], $this->tid));
    }
    
    protected function mergeTemporaryTable() {
        $mergeTemporaryTableSQL = "INSERT INTO $this->_name (id, parentId, l, r, tid, name, body) SELECT id, parentId, l, r, tid, name, body FROM TemporaryTreeTable;";
        $this->getAdapter()->query($mergeTemporaryTableSQL);
    }
    
    protected function dropTemporaryTable() {
        $dropTemporaryTableSQL = "DROP TABLE TemporaryTreeTable;";
        $this->getAdapter()->query($dropTemporaryTableSQL);
    }
    
    protected function parseNode(array $r){
        $r['childNodes']=array();
        unset($r['tid']);
        
        return $r;
    }
}

