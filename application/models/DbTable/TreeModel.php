<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TreeModel
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Application_Model_DbTable_TreeModel extends Application_Model_DbTable_AbstractTreeModel{
    
    protected $_name = "TestTree";
    
    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM Tree WHERE id = ?", array($id));
    }

    protected function insertNode($l, $r, $parentId, $value) {
        $r['childNodes']=array();
        unset($r['tid']);
        
        return $r;
    }
    
    protected function createTemporaryTable(){
        $temporaryTreeTableDDLSQL = "CREATE TEMPORARY TABLE TemporaryTreeTable( id INT UNSIGNED NOT NULL, parentId INT UNSIGNED, l INT UNSIGNED, r INT UNSIGNED, tid INT UNSIGNED, value VARCHAR(255));";
        $this->getAdapter()->query($temporaryTreeTableDDLSQL);
    }
    
    protected function moveNodeIntoTemporaryTable($node) {

        $moveNodeIntoTemporaryTableSQL = "INSERT INTO TemporaryTreeTable (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM Tree WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($moveNodeIntoTemporaryTableSQL, array($node['l'], $node['r'], $this->tid));
    }
    
    protected function mergeTemporaryTable() {
        $mergeTemporaryTableSQL = "INSERT INTO Tree (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM TemporaryTreeTable;";
        $this->getAdapter()->query($mergeTemporaryTableSQL);
    }
    
    protected function dropTemporaryTable() {
        $dropTemporaryTableSQL = "DROP TABLE TemporaryTreeTable;";
        $this->getAdapter()->query($dropTemporaryTableSQL);
    }
    
    protected function parseNode(array $r){
        return array(
                "id" => $r['id'],
                'l' => $r['l'],
                'r' => $r['r'],
                'parentId' => $r['parentId'],
                'childNodes' => array()
            );
    }
}