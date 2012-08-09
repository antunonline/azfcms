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
    protected $_contextMenu;

    /**
     *
     * @var int 
     */
    protected $_contextId;

    /**
     *
     * @var array
     */
    protected $_levelMenuCache = array();

    /**
     *
     * @var array 
     */
    protected $_breadCrumbs;

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
        $node['url'] = "/" . trim(urlencode($node['url']), '/') . "/" . $node['id'] . ".html";

        foreach ($node['childNodes'] as &$cnode) {
            $this->_parseNodes($cnode);
        }
    }

    /**
     * Return menu tree
     * @return array
     */
    public function getTree() {

        if ($this->_getMenuTree() === null) {
            $menuNodes = $this->getNavigation()->getTree(array(
                'id', 'url', 'title'
                    ));

            $this->_parseNodes($menuNodes);
            $this->_setMenuTree($menuNodes);
        }
        return $this->_getMenuTree();
    }

    /**
     *
     * @param array $menuTree
     * @param int $contextId 
     * @return array
     */
    protected function _searchContextMenu(array &$menuTree, $contextId) {
        if ($menuTree['id'] == $contextId) {
            return $menuTree;
        } else {
            foreach ($menuTree['childNodes'] as $node) {
                $checkNode = $this->_searchContextMenu($node, $contextId);
                if ($checkNode) {
                    return $checkNode;
                }
            }
        }
    }

    /**
     *
     * @param array $_breadCrumbs 
     */
    public function setBreadCrumbs(array$_breadCrumbs) {
        $this->_breadCrumbs = $_breadCrumbs;
    }

    /**
     *
     * @param array $menuTree
     * @param int $contextId
     * @param array $breadcrumbs
     * @return array 
     */
    protected function _constructBreadCrumbs(array $menuTree, $contextId, array &$breadcrumbs) {
        if ($menuTree['id'] == $contextId) {
            return true;
        } else {
            foreach ($menuTree['childNodes'] as $node) {
                $outcome = $this->_constructBreadCrumbs($node, $contextId, $breadcrumbs);
                if ($outcome) {
                    unset($node['childNodes']);
                    $breadcrumbs[] = $node;
                    return true;
                }
            }
        }
    }

    protected function _findContextMenu($tree, $contextId) {
        if ($tree['id'] == $contextId) {
            return $tree['childNodes'];
        }

        for ($i = 0, $len = sizeof($tree['childNodes']); $i < $len; $i++) {
            $return = $this->_findContextMenu($tree['childNodes'][$i], $contextId);
            if ($return) {
                return $return;
            }
        }
    }
    
    /**
     * 
     * @param int $level
     * @return array
     */
    protected function _findNLevelMenu($level) {
        if(isset($this->_levelMenuCache[$level])){
            return $this->_levelMenuCache[$level];
        }
        $breadCrumbs = $this->getBreadCrumbs();
        $len = sizeof($breadCrumbs);
        $tree = $this->getTree();
        $nodes = $tree['childNodes'];




        for ($i =0, $cachedLevels; $i < $len && $i < $level; $i++) {
            $searchId = $breadCrumbs[$i]['id'];

            for ($i1 = 0, $len1 = sizeof($nodes); $i1 < $len1; $i1++) {
                if ($nodes[$i1]['id'] == $searchId) {
                    $nodes = $nodes[$i1]['childNodes'];
                    break;
                }
            }
        }

        if ($i < $level) {
            $nodes = array();
        }


        return $this->_levelMenuCache[$level] = $nodes;
    }

    /**
     * 
     * @return Array
     */
    public function getRootMenu() {
        $tree = $this->getTree();
        return $tree['childNodes'];
    }

    /**
     * 
     */
    public function getBreadCrumbs() {
        if (empty($this->_breadCrumbs)) {
            $tree = $this->getTree();
            $breadCrumbs = array();
            $this->_constructBreadCrumbs($tree, $this->getContextId(), $breadCrumbs);
            $breadCrumbs = array_reverse($breadCrumbs);
            $this->setBreadCrumbs($breadCrumbs);
        }
        return $this->_breadCrumbs;
    }

    public function getFirstLevelMenu() {
        return $this->_findNLevelMenu(0);
    }

    public function getSecondLevelMenu() {
        return $this->_findNLevelMenu(1);
    }
    
    public function getNLevelMenu($level){
        return $this->_findNLevelMenu($level);
    }

    public function getContextMenu() {
        if ($this->_contextMenu !== null) {
            return $this->_contextMenu;
        }
        return $this->_contextMenu = $this->_findContextMenu($this->getTree(), $this->getContextId());
    }

}
