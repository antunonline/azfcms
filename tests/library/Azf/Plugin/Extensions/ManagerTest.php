<?php

class Application_Plugin_Extension_ManagerTestSetUp extends Azf_Plugin_Extension_Abstract {

    public static $lastInstance;
    public static $firstInstance;

    public function __construct(array $params = array()) {
        parent::__construct($params);
        self::$lastInstance = $this;
        if (is_null(self::$firstInstance)) {
            self::$firstInstance = $this;
        }
    }

    public $isSetUp = 0;
    public $isTearDown = 0;
    public $isRender = 0;

    public function setUp() {
        self::$firstInstance->isSetUp++;
    }

    public function tearDown() {
        self::$firstInstance->isTearDown++;
    }

    public function render() {
        $this->isRender++;
        if ($this != self::$firstInstance) {
            self::$firstInstance->isRender++;
        }

        $params = $this->getParams();
        if (isset($params['key'])) {
            echo $params['key'];
        }
    }

}

class Application_Plugin_Extension_ManagerTestSetUpParams extends Azf_Plugin_Extension_Abstract {

    public function render() {
        
    }

    public function setUp() {
        $this->setParam("test", "test");
    }

    public function tearDown() {
        
    }
}

class Application_Plugin_Extension_ManagerTestTearDownParams extends Azf_Plugin_Extension_Abstract {

    public function render() {
        
    }

    public function setUp() {
        
    }

    public function tearDown() {
        $this->setParam("test", "test");
    }
}

/**
 * 
 *
 * @author antun
 */
class Azf_Plugin_Extension_ManagerTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        Zend_Registry::set("db", Azf_PHPUnit_Db_ConnectionFactory::getConnection());
        Zend_Db_Table_Abstract::setDefaultAdapter(Zend_Registry::get("db"));
    }

    /**
     * 
     * @param array $methods
     * @param string $pluginClassName
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getPluginMock($methods, $pluginClassName) {
        $pluginClassName = is_string($pluginClassName) ? $pluginClassName : "";
        return $this->getMockForAbstractClass("Azf_Plugin_Extension_Abstract", $methods, $pluginClassName, false);
    }

    /**
     * 
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getModelMock($methods) {
        return $this->getMockBuilder("Azf_Model_DbTable_Plugin")
                ->disableOriginalConstructor()->setMethods($methods)
                ->getMock();
    }

    /**
     * @return Azf_Plugin_Extension_Manager
     */
    public function getManager() {
        return new Azf_Plugin_Extension_Manager();
    }

    public function testSetModel() {
        $manager = $this->getManager();

        $model = new Azf_Model_DbTable_Plugin();
        $manager->setModel($model);
        $actual = $manager->getModel();

        $this->assertEquals($model, $actual);
    }

    public function testGetModel() {
        $this->assertTrue($this->getManager()->getModel() instanceof Azf_Model_DbTable_Plugin);
    }

    public function testSetUp() {
        $params = array(
            'key' => 'value'
        );

        $modelMock = $this->getModelMock(array('getPluginParams'));
        $modelMock->expects($this->once())
                ->method("getPluginParams")
                ->with(2)->will($this->returnValue($params));

        $manager = $this->getManager();

        $manager->setModel($modelMock);
        Application_Plugin_Extension_ManagerTestSetUp::$firstInstance = null;
        $manager->setUp("managerTestSetUp", 2);

        $this->assertEquals(1, Application_Plugin_Extension_ManagerTestSetUp::$firstInstance->isSetUp);
        $this->assertEquals(2, Application_Plugin_Extension_ManagerTestSetUp::$lastInstance->getId());
    }
    
    
    public function testSetUpParamsSave(){
        $modelMock = $this->getModelMock(array('setPluginParams','getPluginParams'));
        $modelMock->expects($this->once())
                ->method("setPluginParams")
                ->with(33,array('test'=>'test'));
        $modelMock->expects($this->once())
                ->method("getPluginParams")
                ->with(33)
                ->will($this->returnValue(array()));
        
        $manager = $this->getManager();
        $manager->setModel($modelMock);
        
        $manager->setUp("managerTestSetUpParams", 33);
    }

    public function testTearDown() {
        $params = array(
            'key' => 'value'
        );

        $modelMock = $this->getModelMock(array('getPluginParams'));
        $modelMock->expects($this->once())
                ->method("getPluginParams")
                ->with(2)->will($this->returnValue($params));

        $manager = $this->getManager();

        $manager->setModel($modelMock);
        Application_Plugin_Extension_ManagerTestSetUp::$firstInstance = null;
        $manager->tearDown("managerTestSetUp", 2);

        $this->assertEquals(1, Application_Plugin_Extension_ManagerTestSetUp::$firstInstance->isTearDown);
        $this->assertEquals(2, Application_Plugin_Extension_ManagerTestSetUp::$lastInstance->getId());
    }
        

    public function testRender() {
        $params = array(
            array(
                'id' => 1,
                'name' => 'name1',
                'description' => 'description1',
                'type' => 'managerTestSetUp',
                'region' => 'left',
                'params' => array('key' => 'value1')
            ),
            array(
                'id' => 2,
                'name' => 'name2',
                'description' => 'description2',
                'type' => 'managerTestSetUp',
                'region' => 'right',
                'params' => array('key' => 'value2')
            ),
            array(
                'id' => 3,
                'name' => 'name3',
                'description' => 'description3',
                'type' => 'managerTestSetUp',
                'region' => 'top',
                'params' => array('key' => 'value3')
            ),
            array(
                'id' => 4,
                'name' => 'name4',
                'description' => 'description4',
                'type' => 'managerTestSetUp',
                'region' => 'bottom',
                'params' => array('key' => 'value4')
            ),
            array(
                'id' => 5,
                'name' => 'name5',
                'description' => 'description5',
                'type' => 'managerTestSetUp',
                'region' => 'bottom',
                'params' => array('key' => 'value5')
            )
        );

        $modelMock = $this->getModelMock(array('findAllByNavigationid'));
        $modelMock->expects($this->once())
                ->method("findAllByNavigationid")
                ->with(2)->will($this->returnValue($params));

        $manager = $this->getManager();

        $manager->setModel($modelMock);

        Application_Plugin_Extension_ManagerTestSetUp::$firstInstance = null;
        $actualRendered = $manager->render(2);
        $expectedRendered = array(
            'left' => 'value1',
            'right' => 'value2',
            'top' => 'value3',
            'bottom' => 'value4value5'
        );
        $this->assertEquals($expectedRendered, $actualRendered);

        $this->assertEquals(5, Application_Plugin_Extension_ManagerTestSetUp::$firstInstance->isRender);
        $this->assertEquals(5, Application_Plugin_Extension_ManagerTestSetUp::$lastInstance->getId());
    }

    public function testGetPluginDefinitions() {
        $expected = array('a', 'b', 'c');
        $model = $this->getModelMock(array('findAllByNavigationid'));
        $model->expects($this->once())
                ->method("findAllByNavigationid")
                ->with(2)
                ->will($this->returnValue($expected));

        $manager = $this->getManager();
        $manager->setModel($model);
        $actual = $manager->getPluginDefinitions(2);
        $this->assertEquals($expected, $actual);
    }

}
