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

    public static function setUpTable() {
        if (!Azf_PHPUnit_Db_Utils::tableExist("Navigation")) {
            Azf_PHPUnit_Db_Utils::createTable("Navigation");
        }
    }

    public static function setUpBeforeClass() {
        Azf_PHPUnit_Db_ConnectionFactory::initDefaultDbTableAdapter();
        parent::setUpBeforeClass();
        self::setUpTable();
    }

    public function truncateTable() {
        Azf_PHPUnit_Db_Utils::truncateTable("Navigation");
    }

    public function fillTable() {
        Azf_PHPUnit_Db_Utils::populateTable(__DIR__ . "/NavigationTestResources/Navigation.xml");
    }

    protected function setUp() {
        parent::setUp();
        $this->truncateTable();
        $this->fillTable();

        $this->navigation = new Azf_Model_Tree_Navigation();
    }

    public function testTrue() {
        $this->assertTrue(true);
    }

    /**
     * @test 
     */
    public function insert_testInsert() {
        $this->navigation->insertBefore(2, array());
    }

    public function staticConfig_ReadTest() {
        $expected = "Cool Title!";
        $actual = $this->navigation->getStaticParam(3, "title");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function staticConfig_readFromChildNode() {
        $expected = "Other text, Cool:)";
        $actual = $this->navigation->getStaticParam(7, "otherTitle");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function staticConfig_readAllParams() {
        $expected = array(
            "action" => "index",
            "controller" => "index",
            "module" => "default"
        );
        $actual = $this->navigation->getStaticParams(8);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function staticConfig_hasParam() {
        $this->assertFalse($this->navigation->hasStaticParam(1, "Test"));

        $this->assertTrue($this->navigation->hasStaticParam(8, "action"));
    }

    /**
     * @test 
     */
    public function staticConfig_setParam() {
        $this->navigation->setStaticParam(1, "firstName", "MyName");
        $newNavigation = new Azf_Model_Tree_Navigation();

        $this->assertEquals("MyName", $newNavigation->getStaticParam(1, "firstName"));
        $this->assertFalse($newNavigation->hasStaticParam(2, "firstName"));
    }

    /**
     * @test 
     */
    public function staticConfig_deleteParam() {
        $this->navigation->deleteStaticParam(8, "action");
        $newNavigation = new Azf_Model_Tree_Navigation();

        $this->assertFalse($this->navigation->hasStaticParam(8, "action"));
        $this->assertFalse($newNavigation->hasStaticParam(8, "action"));

        $this->assertTrue($this->navigation->hasStaticParam(8, "controller"));
        $this->assertTrue($this->navigation->hasStaticParam(8, "module"));
        $this->assertTrue($newNavigation->hasStaticParam(8, "controller"));
        $this->assertTrue($newNavigation->hasStaticParam(8, "module"));
    }

    /**
     * @test 
     */
    public function dynamicConfig_HasParam() {
        $this->assertTrue($this->navigation->hasDynamicParam(1, "title"));
        $this->assertTrue($this->navigation->hasDynamicParam(7, "title"));
    }

    /**
     * @test 
     */
    public function dynamicConfiguration_getParams() {
        $expected = array(
            "title" => "AZFCMS",
            "param1" => "sometext"
        );

        $actual = $this->navigation->getDynamicParams(2);
        $this->assertEquals($expected, $actual);
    }

    /**
     *  @test
     */
    public function dynamicConfiguration_getParamsForNotSetRecord() {
        $this->setExpectedException("RuntimeException");
        $this->navigation->getDynamicParams(2222);
    }

    /**
     * @test 
     */
    public function dynamicConfiguration_getParam() {
        $expected = "YES";
        $actual = $this->navigation->getDynamicParam(3, "accept");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function dynamicConfiguration_getUndefinedParam() {
        $expected = false;
        $actual = $this->navigation->getDynamicParam(1, "missingParam", $expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function dynamicConfiguration_setParam() {
        $expected = "VALUE";
        $this->navigation->setDynamicParam(4, "newParam", $expected);
        $navigation = $this->navigation;
        $newNavigation = new Azf_Model_Tree_Navigation();


        $this->assertEquals($expected, $navigation->getDynamicParam(4, "newParam"));
        $this->assertEquals($expected, $navigation->getDynamicParam(7, "newParam"));
        $this->assertEquals($expected, $navigation->getDynamicParam(6, "newParam"));
        $this->assertEquals("AZFCMS", $navigation->getDynamicParam(6, "title"));


        $navigation = $newNavigation;
        $this->assertEquals($expected, $navigation->getDynamicParam(4, "newParam"));
        $this->assertEquals($expected, $navigation->getDynamicParam(7, "newParam"));
        $this->assertEquals($expected, $navigation->getDynamicParam(6, "newParam"));
        $this->assertEquals("AZFCMS", $navigation->getDynamicParam(6, "title"));
    }

    /**
     * @test
     */
    public function dynamicConfiguration_deleteParam() {
        $navi = $this->navigation;
        $navi->deleteDynamicParam(1, "title");

        $this->assertFalse($navi->hasDynamicParam(1, "title"));
        $this->assertFalse($navi->hasDynamicParam(2, "title"));
        $this->assertTrue($navi->hasDynamicParam(3, "accept"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertFalse($navi->hasDynamicParam(1, "title"));
        $this->assertFalse($navi->hasDynamicParam(2, "title"));
        $this->assertTrue($navi->hasDynamicParam(3, "accept"));
    }

    /**
     *  @test
     */
    public function pluginsConfiguration_getDescriptionParam() {
        $expected = "description";

        $this->assertEquals($expected, $this->navigation->getPluginParam(1, "description", "meta"));
        $this->assertEquals($expected, $this->navigation->getPluginParam(3, "description", "meta"));
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_getDescriptionParamWithInheritance() {
        $expected = "overriden";
        $actual = $this->navigation->getPluginParam(4, "description", "meta");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function pluginsConfiguration_getDescriptionParams() {

        $expected = array("meta" => "description");

        $actual = $this->navigation->getPluginParams(1, "description");
        $this->assertEquals($expected, $actual);

        $actual = $this->navigation->getPluginParams(2, "description");
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_getDescriptionParamsWithInheritance() {
        $expected = array(
            "what" => "YEA",
            "meta" => "description"
        );
        $actual = $this->navigation->getPluginParams(3, "description");

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function pluginsConfiguration_hasParam() {
        $this->assertFalse($this->navigation->hasPluginParam(1, "meta", "meta"));
        $this->assertFalse($this->navigation->hasPluginParam(1, "description", "none"));

        $navi = $this->navigation;
        $this->assertTrue($navi->hasPluginParam(1, "description", "meta"));
        $this->assertTrue($navi->hasPluginParam(2, "description", "meta"));
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_setParam() {
        $navi = $this->navigation;
        $navi->setPluginParam(1, "description", "hello", "world");

        $this->assertEquals("world", $navi->getPluginParam(1, "description", "hello"));
        $this->assertEquals("world", $navi->getPluginParam(2, "description", "hello"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertEquals("world", $navi->getPluginParam(1, "description", "hello"));
        $this->assertEquals("world", $navi->getPluginParam(2, "description", "hello"));
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_setParamOverrideParent() {
        $navi = $this->navigation;
        $navi->setPluginParam(1, "description", "meta", "world");

        $this->assertEquals("world", $navi->getPluginParam(1, "description", "meta"));
        $this->assertEquals("world", $navi->getPluginParam(2, "description", "meta"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertEquals("world", $navi->getPluginParam(1, "description", "meta"));
        $this->assertEquals("world", $navi->getPluginParam(2, "description", "meta"));
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_setParamOfUndefinedPlugin() {
        $navi = $this->navigation;
        $navi->setPluginParam(2, "new", "hello", "new");
        $actual = "new";

        $this->assertEquals($actual, $navi->getPluginParam(2, "new", "hello"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertEquals($actual, $navi->getPluginParam(2, "new", "hello"));
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_deleteParentParam() {
        $navi = $this->navigation;
        $navi->deletePluginParam(1, "description", "meta");

        $this->assertFalse($navi->hasPluginParam(1, "description", "meta"));
        $this->assertFalse($navi->hasPluginParam(2, "description", "meta"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertFalse($navi->hasPluginParam(1, "description", "meta"));
        $this->assertFalse($navi->hasPluginParam(2, "description", "meta"));
    }

    /**
     * @test
     */
    public function pluginsConfiguration_deleteChildParam() {
        $navi = $this->navigation;
        $navi->deletePluginParam(4, "info", "info");

        $this->assertFalse($navi->hasPluginParam(4, "info", "info"));
        $this->assertTrue($navi->hasPluginParam(4, "description", "meta"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertFalse($navi->hasPluginParam(4, "info", "info"));
        $this->assertTrue($navi->hasPluginParam(4, "description", "meta"));
    }

    /**
     * @test
     */
    public function pluginsConfiguration_getPluginsNames() {
        $navi = $this->navigation;
        $expected = array("description");

        $actual = $navi->getPluginNames(1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_getPluginsNamesFromChild() {
        $navi = $this->navigation;
        $expected = array("description", "info");

        $actual = $navi->getPluginNames(4);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function pluginsConfiguration_deletePlugin() {
        $navi = $this->navigation;

        $navi->deletePlugin(4, "description");
        $this->assertEquals("description", $navi->getPluginParam(4, "description", "meta"));
        $this->assertTrue($navi->hasPluginParam(4, "info", "info"));

        $navi = new Azf_Model_Tree_Navigation();
        $this->assertEquals("description", $navi->getPluginParam(4, "description", "meta"));
        $this->assertTrue($navi->hasPluginParam(4, "info", "info"));
    }

    /**
     * @test 
     */
    public function disable_page3WithoutChildNodes() {
        $navi = $this->navigation;

        $navi->disable(3);

        $expected = "1";
        $node = $navi->find(3);
        $actual = $node['disabled'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function disable_page4WithChildNodes() {
        $navi = $this->navigation;
        $navi->disable(4);
        $expected = "1";

        $node = $navi->find(4);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(6);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(7);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(8);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(3);
        $actual = $node['disabled'];
        $this->assertEquals("0", $actual);
    }

    /**
     * @test 
     */
    public function enable_page2() {
        $navi = $this->navigation;
        $navi->disable(2);
        $navi->enable(2);

        $node = $navi->find(2);
        $actual = $node['disabled'];
        $this->assertEquals("0", $actual);
    }

    /**
     * @test 
     */
    public function enable_page4WithChildNodes() {
        $navi = $this->navigation;
        $navi->disable(4);
        $navi->enable(4);
        $expected = "0";

        $node = $navi->find(4);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(6);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(7);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(8);
        $actual = $node['disabled'];
        $this->assertEquals($expected, $actual);

        $node = $navi->find(3);
        $actual = $node['disabled'];
        $this->assertEquals("0", $actual);
    }

    /**
     * @test
     */
    public function menu_getBreadCrumbsMenu() {
        $navi = $this->navigation;
        $expected = array(
            array(
                "id" => 2,
                "url" => "/products"
            )
        );

        $actual = $navi->getBreadCrumbsMenu(2, 1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function menu_getBreadCrumbsMenuForChild() {
        $navi = $this->navigation;
        $expected = array(
            array(
                "id" => 4,
                "url" => "/forums"
            ),
            array(
                "id" => 6,
                "url" => "/forums/support"
            )
        );

        $actual = $navi->getBreadCrumbsMenu(6, 1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test 
     */
    public function navigation_getBreadCrumbsMenuWithInsufficientACLRights() {
        $navi = $this->navigation;
        $expected = array(
            array(
                "id" => 4,
                "url" => "/forums"
            )
        );


        Azf_PHPUnit_Db_SchemaUtils::unbindNavigation_ACLGroup(6, 1);

        $actual = $navi->getBreadCrumbsMenu(6, 1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function navigation_getRootMenu() {
        $navi = $this->navigation;
        $expected = array(
            array('id' => '2', 'url' => '/products'),
            array('id' => '3', 'url' => '/support'),
            array('id' => '4', 'url' => '/forums')
        );

        $actual = $navi->getRootMenu(1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function navigation_getRootMenuWithACL() {
        $navi = $this->navigation;
        $expected = array(
            array('id' => '2', 'url' => '/products'),
            array('id' => '4', 'url' => '/forums')
        );

        Azf_PHPUnit_Db_SchemaUtils::unbindNavigation_ACLGroup(3, 1);
        $actual = $navi->getRootMenu(1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function navigation_getRootMenuForUndefinedUser() {
        $navi = $this->navigation;
        $expected = array(
        );


        $actual = $navi->getRootMenu(33);
        $this->assertEquals($expected, $actual);
    }

    /**
     *  @test
     */
    public function navigation_getContextualMenu() {
        $navi = $this->navigation;
        $expected = array(
            array('id' => '2', 'url' => '/products'),
            array('id' => '3', 'url' => '/support'),
            array('id' => '4', 'url' => '/forums')
        );
        
        $actual = $navi->getContextualMenu(1, 1);
        $this->assertEquals($expected, $actual);
    }
    
    
    /**
     * @test 
     */
    public function navigation_getContextualMenuForSubNode(){
        $navi = $this->navigation;
        $expected = array(
            array('id' => '6', 'url' => '/forums/support'),
            array('id' => '7', 'url' => '/forums/producta'),
            array('id' => '8', 'url' => '/forums/other')
        );
        
        $actual = $navi->getContextualMenu(4, 1);
        $this->assertEquals($expected, $actual);
    }
    
    
    /**
     *@test 
     */
    public function navigation_getContextualMenuForEmptyNode(){
        $navi = $this->navigation;
        $expected = array(
        );
        
        $actual = $navi->getContextualMenu(3, 1);
        $this->assertEquals($expected, $actual);
    }
    
    
    /**
     *@test 
     */
    public function navigation_getContextualMenuForUndefinedUser(){
        $navi = $this->navigation;
        $expected = array(
        );
        
        $actual = $navi->getContextualMenu(1, 3);
        $this->assertEquals($expected, $actual);
    }

}
