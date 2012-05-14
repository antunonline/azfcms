<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Navigation
 *
 * @author antun
 */
class Azf_View_Helper_Navigation extends Zend_View_Helper_Abstract {

    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigation;

    /**
     *
     * @var array
     */
    protected $_menuTree;
    
    
    /**
     *
     * @var array 
     */
    protected $_contextMenus;
    
    /**
     *
     * @var int 
     */
    protected $_contextId;

    public function direct() {
        return $this;
    }

    /**
     *
     * @return Azf_Model_Tree_Navigation
     */
    protected function getNavigation() {
        if (empty($this->navigation)) {
            $this->setNavigation(Zend_Registry::get("navigationModel"));
        }

        return $this->navigation;
    }

    /**
     *
     * @return array
     */
    public function _getMenuTree() {
        return $this->_menuTree;
    }

    /**
     *
     * @param array $menuTree 
     */
    public function _setMenuTree(array $menuTree) {
        $this->_menuTree = $menuTree;
    }
    
    /**
     *
     * @param array $menuTree 
     */
    public function setMenuTree(array $menuTree) {
        $this->_menuTree = $menuTree;
    }

    /**
     *
     * @param array $contextMenus 
     */
    public function setContextMenus(array $contextMenus) {
        $this->_contextMenus = $contextMenus;
    }
    
    /**
     *
     * @return int 
     */
    public function getContextId() {
        return $this->_contextId;
    }

    /**
     *
     * @param int $contextId 
     */
    public function setContextId($contextId) {
        $this->_contextId = $contextId;
    }
    

    
        

    /**
     *
     * @param Azf_Model_Tree_Navigation $navigation 
     */
    public function setNavigation(Azf_Model_Tree_Navigation $navigation) {
        $this->navigation = $navigation;
    }

    protected function _parseNodes(&$node) {
        $node['url'] = "/".  trim($node['url'],'/')."/".$node['id'].".html";
        
        foreach($node['childNodes'] as &$cnode) {
            $this->_parseNodes($cnode);
        }
    }

    /**
     * Return menu tree
     * @return array
     */
    public function getMenuTree() {
        
        if ($this->_getMenuTree() === null) {
            $menuNodes = $this->getNavigation()->getTree(array(
                'id', 'url','title'
                    ));
            
            $this->_parseNodes($menuNodes);
            $this->_setMenuTree($menuNodes);
        }
        return $this->_getMenuTree();
    }
    
    
    public function getContextMenus(){
        if($this->_contextMenus!== null){
            return $this->_contextMenus;
        }
        else {
            $this->_initContextMenus();
            return $this->_contextMenus;
            
        }
    }
    
    /**
     * Initialize context menu 
     */
    protected function _initContextMenus(){
        $contextId = $this->getContextId();
        $menuTree = $this->getMenuTree();
        
        $contextMenus = $this->_searchContextMenu($menuTree, $contextId);
        $this->setContextMenus($contextMenus);
    }
    
    /**
     *
     * @param array $menuTree
     * @param int $contextId 
     * @return array
     */
    protected function _searchContextMenu(array &$menuTree,$contextId){
        if($menuTree['id']==$contextId){
            return $menuTree;
        } else {
            foreach($menuTree['childNodes'] as $node){
                $checkNode = $this->_searchContextMenu($node, $contextId);
                if($checkNode){
                    return $checkNode;
                }
            }
        }
    }

}
