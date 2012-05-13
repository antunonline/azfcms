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
class Application_View_Helper_Navigation extends Zend_View_Helper_Abstract {

    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigation;

    /**
     *
     * @var array
     */
    protected $menuTree;

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

    public function _getMenuTree() {
        return $this->menuTree;
    }

    public function _setMenuTree($menuTree) {
        $this->menuTree = $menuTree;
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
        $node['title'] = $node['url'];
        
        foreach($node['childNodes'] as &$cnode) {
            $this->_parseNodes($cnode);
        }
    }

    public function getMenuTree() {
        if ($this->_getMenuTree() === null) {
            $menuNodes = $this->getNavigation()->getTree(array(
                'id', 'url'
                    ));
            
            $this->_parseNodes($menuNodes);
            $this->_setMenuTree($menuNodes);
        }
        return $this->_getMenuTree();
    }

}
