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
abstract class Azf_Model_Tree_Abstract extends Zend_Db_Table_Abstract {

    protected $tid = 1;

    public function getTid() {
        return $this->tid;
    }

    public function setTid($tid) {
        if (!ctype_digit((string) $tid) || (int) $tid < 1) {
            throw new InvalidArgumentException("Input value is not a valid unsigned integer");
        }
        $this->tid = $tid;
    }

    abstract public function _find($id);

    abstract protected function insertNode($l, $r, $parentId, $value);

    abstract protected function createTemporaryTable();
    
    abstract protected function getTemporaryTableName();

    abstract protected function moveNodeIntoTemporaryTable($node);

    abstract protected function mergeTemporaryTable();

    abstract protected function dropTemporaryTable();

    abstract protected function parseNode(array $r);

    public function find($id) {
        return $this->_find($id);
    }

    protected function _insertNode($l, $r, $parentId, $value) {
        return $this->insertNode($l, $r, $parentId, $value);
    }

    protected function _parseNode(array $r) {
        $n = $this->parseNode($r);

        return $n;
    }

    protected function _startTransaction() {
        $this->getAdapter()->query("begin");
        $this->_lockTable();
    }
    
    protected function _lockTable(){
//        $this->getAdapter()->query("LOCK TABLES $this->_name WRITE");
    }

    protected function _endTransaction() {
        $this->getAdapter()->query("commit");
        $this->_unlockTable();
    }
    
    protected function _unlockTable(){
//        $this->getAdapter()->query("UNLOCK TABLES");
    }

    protected function _rollBackTransaction() {
        $this->getAdapter()->query("rollback ");
        $this->_unlockTable();
    }

    protected function _createTemporaryTable() {
        $this->createTemporaryTable();
    }

    protected function _moveNodeIntoTemporaryTable($node) {
        $this->moveNodeIntoTemporaryTable($node);
    }

    protected function _mergeTemporaryTable() {
        $this->mergeTemporaryTable();
    }

    protected function _dropTemporaryTable() {
        $this->dropTemporaryTable();
    }

    protected function _parseCols($cols = null, $tablePrefix = null) {

        if (is_string($cols)) {
            $cols = array($cols);
        } else if (is_array($cols)) {
            unset($cols['l']);
            unset($cols['r']);

            if (false !== $key = array_search("l", $cols)) {
                unset($cols[$key]);
            }
            if (false !== $key = array_search("r", $cols)) {
                unset($cols[$key]);
            }
        } else {
            $cols = array("*");
        }

        $cols[] = "l";
        $cols[] = "r";


        if (is_null($tablePrefix)) {
            $cols = implode(",", $cols);
        } else {
            $tmpCols = array();
            foreach ($cols as $key => $value) {
                if($value != "*"){
                    $value = "`$value`";
                }
                
                if (is_numeric($key)) {
                    $tmpCols[] = "`$tablePrefix`.$value";
                } else {
                    $tmpCols[] = "`$tablePrefix`.$value AS `$key`";
                }
            }
            $cols = implode(", ", $tmpCols);
            unset($tmpCols);
        }

        return $cols;
    }

    public function initTree($value) {
        $this->_startTransaction();
        $selectSQL = "SELECT * FROM $this->_name WHERE l = 1 AND tid = ?;";
        if (false !== $this->getAdapter()->fetchRow($selectSQL, array($this->tid))) {
            $this->_rollBackTransaction();
            throw new RuntimeException("Tree is already initialized");
        }

        $this->_insertNode(1, 2, null, $value);
        $this->_endTransaction();
    }
    
    public function isTreeValid(){
        $SQL = "SELECT count(id) as `count`, sum(l)+sum(r) as `sum` FROM $this->_name WHERE tid = $this->tid;";
        $result = $this->getAdapter()->fetchRow($SQL);
        
        $rowCount = $result['count'];
        $actualSum = $result['sum'];
        $n = $rowCount*2;
        
        $expectedSum = ($n*($n+1))/2;
        if($expectedSum==$actualSum){
            return true;
        } else {
            return false;
        }
    }

    public function insertInto($id, $value) {
        $this->_startTransaction();

        $dstNode = $this->_find($id);
        if (!$dstNode) {
            $this->_rollBackTransaction();
            return false;
        }


        $updateParentNodesSQL = "UPDATE $this->_name SET r = r+2 WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($updateParentNodesSQL, array($dstNode['l'], $dstNode['r'], $this->tid));

        $updateNextSiblingsSQL = "UPDATE $this->_name SET l = l+2, r = r+2 WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($updateNextSiblingsSQL, array($dstNode['r'], $dstNode['r'], $this->tid));

        $newNode = $this->_insertNode($dstNode['r'], $dstNode['r'] + 1, $dstNode['id'], $value);

        
        if($this->isTreeValid()){
            $this->_endTransaction();
            return $newNode;
        } else {
            $this->_rollBackTransaction();
            return false;
        }
    }

    public function insertAfter($id, $value) {
        $this->_startTransaction();

        $node = $this->_find($id);
        if (!$node || $node['l'] == "1") {
            $this->_rollBackTransaction();
            return false;
        }

        $updateParentNodesSQL = "UPDATE $this->_name SET r=r+2 WHERE l < ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($updateParentNodesSQL, array($node['l'], $node['r'], $this->tid));

        $updateNextSiblingsSQL = "UPDATE $this->_name SET l=l+2, r=r+2 WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($updateNextSiblingsSQL, array($node['r'], $node['r'], $this->tid));

        $newNode = $this->_insertNode($node['r'] + 1, $node['r'] + 2, $node['parentId'], $value);


        if($this->isTreeValid()){
            $this->_endTransaction();
            return $newNode;
        } else {
            $this->_rollBackTransaction();
            return false;
        }
    }

    public function insertBefore($id, $value) {
        $this->_startTransaction();

        $node = $this->_find($id);
        if (!$node) {
            $this->_rollBackTransaction();
            return false;
        }

        $updateParentNodesSQL = "UPDATE $this->_name SET r=r+2 WHERE l < ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($updateParentNodesSQL, array($node['l'], $node['r'], $this->tid));

        $updateNextSiblingsSQL = "UPDATE $this->_name SET l=l+2,  r=r+2 WHERE l >= ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($updateNextSiblingsSQL, array($node['l'], $node['l'], $this->tid));

        $newNode = $this->_insertNode($node['l'], $node['r'], $node['parentId'], $value);


        if($this->isTreeValid()){
            $this->_endTransaction();
            return $newNode;
        } else {
            $this->_rollBackTransaction();
            return false;
        }
    }

    public function delete($id) {
        $this->_startTransaction();

        $node = $this->_find($id);
        if (!$node) {
            $this->_rollBackTransaction();
            return false;
        }

        $nodeSize = abs($node['r'] - $node['l']) + 1;

        $deleteNodesSQL = "DELETE FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($deleteNodesSQL, array($node['l'], $node['r'], $this->tid));

        $decreaseParentsSizeSQL = "UPDATE $this->_name SET r = r-? WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($decreaseParentsSizeSQL, array($nodeSize, $node['l'], $node['r'], $this->tid));

        $decreaseNextSiblingsSizeSQL = "UPDATE $this->_name SET r = r-?, l = l-? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($decreaseNextSiblingsSizeSQL, array($nodeSize, $nodeSize, $node['r'], $node['r'], $this->tid));

        
        if($this->isTreeValid()){
            $this->_endTransaction();
            return true;
        } else {
            $this->_rollBackTransaction();
            return false;
        }
    }

    public function deleteTree() {
        $this->getAdapter()->query("DELETE FROM $this->_name WHERE tid = ?", array($this->tid));
    }

    public function moveInto($targetId, $dstId) {
        $moveNode = $this->_find($targetId);
        $intoNode = $this->_find($dstId);
        $temporaryTableName = $this->getTemporaryTableName();

        if (!$moveNode || !$intoNode) {
            return false;
        }

        if ($moveNode['l'] < $intoNode['l'] && $moveNode['r'] > $intoNode['r']) {
            return false;
        }
        
        $this->_createTemporaryTable();
        $this->_startTransaction();

        $targetNodeSize = abs($moveNode['l'] - $moveNode['r']) + 1; 
        if ($intoNode['l'] > $moveNode['l']) {
            $intoNode['l']-=$targetNodeSize;
            $intoNode['r']-=$targetNodeSize;
        }else if($intoNode['l']==1){
            $intoNode['r']-=$targetNodeSize;
        }

        $this->_moveNodeIntoTemporaryTable($moveNode);

        $deleteTargetNodeSQL = "DELETE FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($deleteTargetNodeSQL, array($moveNode['l'], $moveNode['r'], $this->tid));

        $decreaseSizeOfDeletedNodeParents = "UPDATE $this->_name SET r = r-? WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($decreaseSizeOfDeletedNodeParents, array($targetNodeSize, $moveNode['l'], $moveNode['r'], $this->tid));

        $decreaseNextSiblingsOfDeletedNode = "UPDATE $this->_name SET l= l-?, r = r-? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($decreaseNextSiblingsOfDeletedNode, array($targetNodeSize, $targetNodeSize, $moveNode['r'], $moveNode['r'], $this->tid));

        $increaseDstParentSizeSQL = "UPDATE $this->_name SET r = r+? WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstParentSizeSQL, array($targetNodeSize, $intoNode['l'], $intoNode['r'], $this->tid));

        $increaseDstNextSiblingsSizeSQL = "UPDATE $this->_name SET l = l+?, r = r+? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstNextSiblingsSizeSQL, array($targetNodeSize, $targetNodeSize, $intoNode['r'], $intoNode['r'], $this->tid));

        $targetNodeIsAfterDstNode = $moveNode['r'] > $intoNode['l'];
        if ($targetNodeIsAfterDstNode) {
            $targetDstDistance = $intoNode['r'] - $moveNode['l'];
        } else {
            $targetDstDistance = $intoNode['r'] - $moveNode['l'];
        }

        $updateTemporaryTablePositionSQL = "UPDATE `$temporaryTableName` SET l = l+?, r = r+?;";
        $this->getAdapter()->query($updateTemporaryTablePositionSQL, array($targetDstDistance, $targetDstDistance));

        $updateTemporaryTableParentIdSQL = "UPDATE `$temporaryTableName` SET parentId = ? WHERE parentId = ? AND tid = ?;";
        $this->getAdapter()->query($updateTemporaryTableParentIdSQL, array($intoNode['id'], $moveNode['parentId'], $this->tid));


        $this->_mergeTemporaryTable();


        if($this->isTreeValid()){
            $this->_endTransaction();
            $this->_dropTemporaryTable();
            return true;
        } else {
            $this->_rollBackTransaction();
            $this->_dropTemporaryTable();
            return false;
        }
    }

    public function moveBefore($targetId, $dstId) {

        $targetNode = $this->_find($targetId);
        $dstNode = $this->_find($dstId);
        $temporaryTableName = $this->getTemporaryTableName();

        if (!$targetNode || !$dstNode) {
            return false;
        }


        if (($targetNode['l'] < $dstNode['l'] && $targetNode['r'] > $dstNode['r']) ||
                $dstNode['l'] == "1") {
            return false;
        }
        
        $this->_createTemporaryTable();
        $this->_startTransaction();

        $targetNodeSize = abs($targetNode['l'] - $targetNode['r']) + 1;

        if ($targetNode['l'] < $dstNode['l']) {
            $decreaseDstNodePointer = $targetNodeSize;
        } else {
            $decreaseDstNodePointer = 0;
        }

        $this->_moveNodeIntoTemporaryTable($targetNode);

        $deleteTargetNodeSQL = "DELETE FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($deleteTargetNodeSQL, array($targetNode['l'], $targetNode['r'], $this->tid));

        $decreaseSizeOfDeletedNodeParents = "UPDATE $this->_name SET r = r-? WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($decreaseSizeOfDeletedNodeParents, array($targetNodeSize, $targetNode['l'], $targetNode['r'], $this->tid));

        $decreaseNextSiblingsOfDeletedNode = "UPDATE $this->_name SET l= l-?, r = r-? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($decreaseNextSiblingsOfDeletedNode, array($targetNodeSize, $targetNodeSize, $targetNode['r'], $targetNode['r'], $this->tid));

        $increaseDstParentSizeSQL = "UPDATE $this->_name SET r = r+? WHERE l < ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstParentSizeSQL, array($targetNodeSize, $dstNode['l'] - $decreaseDstNodePointer, $dstNode['l'] - $decreaseDstNodePointer, $this->tid));

        $increaseDstNextSiblingsSizeSQL = "UPDATE $this->_name SET l = l+?, r = r+? WHERE l >= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstNextSiblingsSizeSQL, array($targetNodeSize, $targetNodeSize, $dstNode['l'] - $decreaseDstNodePointer, $dstNode['l'] - $decreaseDstNodePointer, $this->tid));

        $targetDstDistance = $dstNode['l'] - $targetNode['l'] - $decreaseDstNodePointer;

        $updateTemporaryTablePositionSQL = "UPDATE `$temporaryTableName` SET l = l+?, r = r+?;";
        $this->getAdapter()->query($updateTemporaryTablePositionSQL, array($targetDstDistance, $targetDstDistance));

        $updateTemporaryTableParentIdSQL = "UPDATE `$temporaryTableName` SET parentId = ? WHERE parentId = ? AND tid = ?;";
        $this->getAdapter()->query($updateTemporaryTableParentIdSQL, array($dstNode['parentId'], $targetNode['parentId'], $this->tid));


        $this->_mergeTemporaryTable();


        if($this->isTreeValid()){
            $this->_endTransaction();
            $this->_dropTemporaryTable();
            return true;
        } else {
            $this->_rollBackTransaction();
            $this->_dropTemporaryTable();
            return false;
        }
    }

    public function moveAfter($targetId, $dstId) {
        $targetNode = $this->_find($targetId);
        $dstNode = $this->_find($dstId);
        $temporaryTableName = $this->getTemporaryTableName();

        if (!$targetNode || !$dstNode) {
            return false;
        }


        if (($targetNode['l'] < $dstNode['l'] && $targetNode['r'] > $dstNode['r']) ||
                ($dstNode['l'] == "1")) {
            return false;
        }
        
        $this->_createTemporaryTable();
        $this->_startTransaction();

        $targetNodeSize = abs($targetNode['l'] - $targetNode['r']) + 1;

        if ($targetNode['l'] < $dstNode['l']) {
            $decreaseDstNodePointer = $targetNodeSize;
            $targetDstDistance = $targetNode['l'] - $targetNode['r'];
        } else {
            $decreaseDstNodePointer = 0;
            $targetDstDistance = 1;
            $increaseTargetNodeSize = 0;
        }

        $this->_moveNodeIntoTemporaryTable($targetNode);

        $deleteTargetNodeSQL = "DELETE FROM $this->_name WHERE l >= ? AND r <= ? AND tid = ?;";
        $this->getAdapter()->query($deleteTargetNodeSQL, array($targetNode['l'], $targetNode['r'], $this->tid));

        $decreaseSizeOfDeletedNodeParents = "UPDATE $this->_name SET r = r-? WHERE l <= ? AND r >= ? AND tid = ?;";
        $this->getAdapter()->query($decreaseSizeOfDeletedNodeParents, array($targetNodeSize, $targetNode['l'], $targetNode['r'], $this->tid));

        $decreaseNextSiblingsOfDeletedNode = "UPDATE $this->_name SET l= l-?, r = r-? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($decreaseNextSiblingsOfDeletedNode, array($targetNodeSize, $targetNodeSize, $targetNode['r'], $targetNode['r'], $this->tid));

        $increaseDstParentSizeSQL = "UPDATE $this->_name SET r = r+? WHERE l < ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstParentSizeSQL, array($targetNodeSize, $dstNode['l'] - $decreaseDstNodePointer, $dstNode['r'] - $decreaseDstNodePointer, $this->tid));

        $increaseDstNextSiblingsSizeSQL = "UPDATE $this->_name SET l = l+?, r = r+? WHERE l > ? AND r > ? AND tid = ?;";
        $this->getAdapter()->query($increaseDstNextSiblingsSizeSQL, array($targetNodeSize, $targetNodeSize, $dstNode['r'] - $decreaseDstNodePointer, $dstNode['r'] - $decreaseDstNodePointer, $this->tid));


        $targetDstDistance += $dstNode['r'] - $targetNode['l'];


        $updateTemporaryTablePositionSQL = "UPDATE `$temporaryTableName` SET l = l+?, r = r+?;";
        $this->getAdapter()->query($updateTemporaryTablePositionSQL, array($targetDstDistance, $targetDstDistance));

        $updateTemporaryTableParentIdSQL = "UPDATE `$temporaryTableName` SET parentId = ? WHERE parentId = ? AND tid = ?;";
        $this->getAdapter()->query($updateTemporaryTableParentIdSQL, array($dstNode['parentId'], $targetNode['parentId'], $this->tid));


        $this->_mergeTemporaryTable();


        if($this->isTreeValid()){
            $this->_endTransaction();
            $this->_dropTemporaryTable();
            return true;
        } else {
            $this->_rollBackTransaction();
            $this->_dropTemporaryTable();
            return false;
        }
    }

    public function getTree($cols = null, $showMetadataColumns = false) {

        $colsSQL = $this->_parseCols($cols);

        $selectSQL = "SELECT $colsSQL From $this->_name WHERE tid = ? ORDER BY l ASC;";
        $selectStmt = $this->getAdapter()->prepare($selectSQL);
        $selectStmt->execute(array($this->tid));

        return $this->_parseTree($selectStmt, $cols, $showMetadataColumns);
    }

    public function getParents($id, $cols = null, $showMetadataColumns = false) {
        $colsSQL = $this->_parseCols($cols, "t");

        $selectSQL = "SELECT $colsSQL from $this->_name t1 
JOIN $this->_name t on t.l < t1.l AND t.r > t1.r AND t.tid = ?
WHERE t1.id = ? AND t1.tid = ?
ORDER BY t.l ASC";
        $selectStmt = $this->getAdapter()->prepare($selectSQL);
        $selectStmt->execute(array($this->tid, $id, $this->tid));

        return $this->_parseTree($selectStmt, $cols,  $showMetadataColumns);
    }
    
    protected function _unsetAllTreeMetadata(&$tree){
        unset($tree['l']);
        unset($tree['r']);
        unset($tree['tid']);
        unset($tree['parentId']);
        
        $len = isset($tree['childNodes']) ? count($tree['childNodes']) : 0;
        for($i = 0; $i < $len; $i++){
            $this->_unsetAllTreeMetadata($tree['childNodes'][$i]);
        }
    }
    
    
    protected function _unsetPartialTreeMetadata(&$tree, $cols){
        foreach($cols as $col){
            unset($tree[$col]);
        }
        
        $len = isset($tree['childNodes']) ? count($tree['childNodes']) : 0;
        for($i = 0; $i < $len; $i++){
            $this->_unsetPartialTreeMetadata($tree['childNodes'][$i], $cols);
        }
        
    }

    protected function _parseTree(Zend_Db_Statement_Interface $stmt, $cols = null, $showMetadataColumns = false) {
        $branch = array();
        $bn = null;
        $s = array();
        $slen = -1;
        
        while (false !== ($r = $stmt->fetch())) {
            $n = $this->_parseNode($r);

            if ($slen !== -1 && ($n['r'] > $s[$slen]['r'])) {
                do {
                    array_pop($s);
                    $slen--;
                } while ($slen !== -1 && ($n['r'] > $s[$slen]['r']));
            }

            if ($n['r'] - $n['l'] > 1) {
                if ($slen === -1) {
                    $branch = &$n;
                    $s[] = &$n;
                } else {
                    $s[$slen]['childNodes'][] = &$n;
                    $s[] = &$n;
                }
                $slen++;
            } else {
                if ($slen > -1) {
                    $s[$slen]['childNodes'][] = &$n;
                } else {
                    $branch = &$n;
                }
            }

            $bn = $n;
            unset($n);
        }
        
        if($showMetadataColumns==false){
            if(is_array($cols) && sizeof($cols) > 0){
                $metadataCols = array('l','r','tid','parentId');
                
                if(4 != count($diff = array_diff($metadataCols, $cols))){
                    $this->_unsetPartialTreeMetadata($branch, $diff);
                }
                else {
                    $this->_unsetAllTreeMetadata($branch, $cols);
                }
            } else {
                $this->_unsetAllTreeMetadata($branch, $cols);
            }
        }

        return $branch ? $branch : false;
        ;
    }

    public function getBranch($id, $cols = null, $showMetadataColumns = false) {
        $n = $this->_find($id);

        if (!$n) {
            return false;
        }

        $colsSQL = $this->_parseCols($cols = null);

        $selectSQL = "SELECT $colsSQL FROM $this->_name WHERE tid = ? AND (l >= ? AND r<=?)  ORDER BY l";
        $stmt = $this->getAdapter()->prepare($selectSQL);
        $stmt->execute(array($this->tid, $n['l'], $n['r']));


        return $this->_parseTree($stmt, $cols, $showMetadataColumns);
    }
    
    
    
    /**
     * 
     * @param int $id
     * @param null|array $cols
     * @return array
     */
    public function getChildren($id, $cols = null) {
        $colsSQL = $this->_parseCols($cols);
        $selectSQL = "SELECT $colsSQL FROM $this->_name WHERE tid = ? AND (parentId = ? OR id = ?)  ORDER BY l,r";
        
        $items =  $this->getAdapter()->fetchAll($selectSQL,array($this->getTid(),$id,$id));
        
        if(isset($items[0])){
            $response  = $items[0];
            $response['childNodes'] = array();
            for($i=1,$len=sizeof($items);$i<$len;$i++){
                $response['childNodes'][] = $items[$i];
            }
        } else  {
            $response = null;
        }
        
        return $response;
    }

    public function getVisible($id, $cols = null, $showMetadataColumns = false) {
        $n = $this->_find($id);
        if (!$n) {
            return false;
        }

        $colsSQL = $this->_parseCols($cols, "t");

        $selectSQL = "SELECT $colsSQL FROM (SELECT * FROM $this->_name t1 WHERE id = ? AND tid = ?) AS t1
JOIN $this->_name t ON t1.id = t.parentId OR t1.parentId = t.id  OR t1.id = t.id or t1.parentId = t.parentId
ORDER BY t.l ASC";
        $stmt = $this->getAdapter()->prepare($selectSQL);
        if ($stmt->execute(array($id, $this->tid))) {
            $branch = $this->_parseTree($stmt, $cols, $showMetadataColumns);
            return $branch;
        } else {
            return false;
        }
    }

    public function getRootNode($cols = null, $showMetadataColumns = false) {
        $colsSQL = $this->_parseCols($cols);

        $selectSQL = "SELECT $colsSQL from $this->_name WHERE tid = ? AND l = ?;";
        $stmt = $this->getAdapter()->prepare($selectSQL);
        $stmt->execute(array($this->tid, 1));
        
        return $this->_parseTree($stmt, $cols, $showMetadataColumns);
    }

}
