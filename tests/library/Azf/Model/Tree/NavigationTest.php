<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NavigationTest
 *
 * @author Antun Horvat <at> it-branch.com
 */
class Azf_Model_Tree_NavigationTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var Azf_Model_Tree_Navigation
     */
    protected $navigation;

    protected function setUp() {
        parent::setUp();
        
        Azf_PHPUnit_Db_DbModel::clean();
        $this->navigation = new Azf_Model_Tree_Navigation();
    }
    
    
    

}
