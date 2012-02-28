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
class Azf_Model_Tree_Default extends Azf_Model_Tree_Abstract{
    
    
    public function _find($id) {
        return $this->getAdapter()->fetchRow("SELECT * FROM $this->_name WHERE id = ?", array($id));
    }

    protected function insertNode($l, $r, $parentId, $value) {
        return $this->insert(array(
                    'l' => $l,
                    'r' => $r,
                    'parentId' => $parentId,
                    'tid' => $this->tid,
                    'value' => $value
                ));
    }
    
    protected function createTemporaryTable(){
        $temporaryTreeTableDDLSQL = "CREATE TEMPORARY TABLE TemporaryTreeTable( id INT UNSIGNED NOT NULL, parentId INT UNSIGNED, l INT UNSIGNED, r INT UNSIGNED, tid INT UNSIGNED, value VARCHAR(255));";
        $this->getAdapter()->query($temporaryTreeTableDDLSQL);
    }
    
    protected function moveNodeIntoTemporaryTable($node) {

        $moveNodeIntoTemporaryTableSQL = "INSERT INTO TemporaryTreeTable (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($moveNodeIntoTemporaryTableSQL, array($node['l'], $node['r'], $this->tid));
    }
    
    protected function mergeTemporaryTable() {
        $mergeTemporaryTableSQL = "INSERT INTO $this->_name (id, parentId, l, r, tid, value) SELECT id, parentId, l, r, tid, value FROM TemporaryTreeTable;";
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

    protected function getTableName() {
        return "Tree";
    }
    
    
    
    
    /**
     * This method will disable current and all sibling nodes
     * @param int $nodeId 
     * @return void
     */
    public function disable($nodeId){
        $this->getAdapter()->query("call navigation_disable (?);",array($nodeId));
    }
    
    
    /**
     * This method will enable current and all sibling nodes
     *
     * @param int $nodeId 
     */
    public function enable($nodeId){
        $this->getAdapter()->query("call navigation_enable (?);",array($nodeId));
    }
    
    
    public function getMenu($userId){
        
    }
}