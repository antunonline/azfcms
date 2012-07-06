<?php
require_once '../application/resolvers/ExtensionPlugin.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateTest
 *
 * @author antun
 */
class Application_Resolver_ExtensionPluginTest extends PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @param type $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getPluginModelMock($methods){
        $mock= $this->getMockBuilder("Azf_Model_DbTable_Plugin")->
                disableOriginalConstructor()->
                setMethods($methods)->getMock();
        
        return $mock;
    }
    
    /**
     * 
     * @param type $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getManagerMock($methods){
        $mock= $this->getMockBuilder("Azf_Plugin_Extension_Manager")->
                disableOriginalConstructor()->
                setMethods($methods)->getMock();
        
        return $mock;
    }
    
    /**
     * 
     * @param type $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getNavigationPluginModelMock($methods){
        $mock =  $this->getMock("Azf_Model_DbTable_NavigationPlugin",array_merge($methods,array('__construct')),array(),'',false);
        return $mock;
    }
    
    
    /**
     * 
     * @param string $className
     */
    public function constructExtensionPluginClass($className){
        $mock = $this->getMockForAbstractClass("Azf_Plugin_Extension_Abstract",array(),  "Application_Resolver_".ucfirst($className),false);
        return $mock;
    }
    
    /**
     * 
     * @param type $template
     * @return Application_Resolver_ExtensionPlugin
     */
    public function getResolver(){
        $resolver = new Application_Resolver_ExtensionPlugin();
        return $resolver;
    }
    
    public function testAddExtensionPlugin(){
        $navigationId=22;
        $name = "name";
        $description = "description";
        $type="addExtensionPlugin";
        $regin = "";
        $weight = 300;
        $enable = false;
        
        $model = $this->getPluginModelMock(array("insertPlugin"));
        $model->expects($this->once())->method("insertPlugin")
                ->with($name,$description,$type,$regin)
                ->will($this->returnValue(3));
        $this->constructExtensionPluginClass("addExtensionPlugin");
        $manager = $this->getManagerMock(array("setUp"));
        $manager->expects($this->once())
                ->method("setUp")
                ->with('addExtensionPlugin',3);
        
        $resolver= $this->getResolver();
        $resolver->setModel($model);
        $resolver->setManager($manager);
        $pluginId = $resolver->addExtensionPluginMethod($navigationId, $name, $description, $type, $regin, $weight, $enable);
        $this->assertEquals(3, $pluginId);
        
    }
    
    public function testAddExtensionPluginCase1(){
        $navigationId=22;
        $name = "name";
        $description = "description";
        $type="addExtensionPluginCase1";
        $regin = "";
        $weight = 300;
        $enable = true;
        
        $model = $this->getPluginModelMock(array("insertPlugin"));
        $model->expects($this->once())->method("insertPlugin")
                ->with($name,$description,$type,$regin)
                ->will($this->returnValue(3));
        $this->constructExtensionPluginClass("Application_Resolver_AddExtensionPluginCase1");
        $manager = $this->getManagerMock(array("setUp"));
        $manager->expects($this->once())
                ->method("setUp")
                ->with('addExtensionPluginCase1',3);
        $navigationPluginModel = $this->getNavigationPluginModelMock(array("bind"));
        $navigationPluginModel->expects($this->once())
                ->method("bind")
                ->with(22,3,300);
        
        $resolver= $this->getResolver();
        $resolver->setModel($model);
        $resolver->setManager($manager);
        $resolver->setNavigationPluginModel($navigationPluginModel);
        $pluginId = $resolver->addExtensionPluginMethod($navigationId, $name, $description, $type, $regin, $weight, $enable);
        $this->assertEquals(3, $pluginId);
    }
    
    public function testRemoveExtensionPlugin(){
        $this->markTestIncomplete("pluginType argument is removed");
        $manager = $this->getManagerMock(array("tearDown"));
        $manager->expects($this->once())
                ->method("tearDown");
        
        $model = $this->getPluginModelMock(array("deleteById"));
        $model->expects($this->once())
                ->method("deleteById")
                ->with(3);
        
        $resolver = $this->getResolver();
        $resolver->setModel($model);
        $resolver->setManager($manager);
        $this->constructExtensionPluginClass("testRemoveExtensionPlugin");
        
        $resolver->removeExtensionPluginMethod("testRemoveExtensionPlugin", 3);
    }
    
    
    public function testEnableExtensionPluginMethod(){
        $model = $this->getNavigationPluginModelMock(array("bind"));
        $model->expects($this->once())
                ->method("bind")
                ->with(3,4,500);
        
        $resolver = $this->getResolver();
        $resolver->setNavigationPluginModel($model);
        
        $resolver->enableExtensionPluginMethod(3, 4, 500);
    }
    
    public function testDisableExtensionPluginMethod() {
        $model = $this->getNavigationPluginModelMock(array("unbind"));
        $model->expects($this->exactly(1))
                ->method("unbind")
                ->with(3,4);
        
        $resolver = $this->getResolver();
        $resolver->setNavigationPluginModel($model);
        $resolver->disableExtensionPluginMethod(3, 4);
    }
    
    public function testGetRegionExtensionPluginsMethod(){
        $expected = array(
            array(1,2,3),
            array(1,2,3),
            array(1,2,3)
        );
        $model = $this->getNavigationPluginModelMock(array("findAllByNavigationAndRegion"));
        $model->expects($this->exactly(1))
                ->method("findAllByNavigationAndRegion")
                ->with(3,4)
                ->will($this->returnValue($expected));
        
        $resolver = $this->getResolver();
        $resolver->setNavigationPluginModel($model);
        
        $this->assertEquals($expected, $resolver->getRegionExtensionPluginsMethod(3, 4));
    }
    
    
    public function testSetExtensionPluginValuesMethod(){
        $pluginModelMock = $this->getPluginModelMock(array('updatePluginValues'));
        $pluginModelMock->expects($this->once())
                ->method("updatePluginValues")
                ->with(23,"name","description","region")
                ->will($this->returnValue(1));
        
        $navigationPluginModelMock = $this->getNavigationPluginModelMock(array("bind"));
        $navigationPluginModelMock->expects($this->once())
                ->method("bind")
                ->with(22,23,400)
                ->will($this->returnValue(1));
        
        $resolver = $this->getResolver();
        $resolver->setModel($pluginModelMock);
        $resolver->setNavigationPluginModel($navigationPluginModelMock);
        
        $resolver->setExtensionPluginValuesMethod(22, 23, "name", "description",  "region", 400, true);
    }
    
    
    public function testSetExtensionPluginValuesMethodCase1(){
        $pluginModelMock = $this->getPluginModelMock(array('updatePluginValues'));
        $pluginModelMock->expects($this->once())
                ->method("updatePluginValues")
                ->with(23,"name","description","region")
                ->will($this->returnValue(1));
        
        $navigationPluginModelMock = $this->getNavigationPluginModelMock(array("unbind"));
        $navigationPluginModelMock->expects($this->once())
                ->method("unbind")
                ->with(22,23)
                ->will($this->returnValue(1));
        
        $resolver = $this->getResolver();
        $resolver->setModel($pluginModelMock);
        $resolver->setNavigationPluginModel($navigationPluginModelMock);
        
        $resolver->setExtensionPluginValuesMethod(22, 23, "name", "description",  "region", 0, false);
    }
    
    public function testFindPluginsByNavigationAndRegionMethod(){
        $navigationPluginModel = $this->getNavigationPluginModelMock(array("findAllByNavigationAndRegion"));
        $navigationPluginModel->expects($this->once())
                ->method("findAllByNavigationAndRegion")
                ->with(33,'left')
                ->will($this->returnValue(array()));
        
        $resolver = $this->getResolver();
        $resolver->setNavigationPluginModel($navigationPluginModel);
        
        $this->assertEquals(array(),$resolver->findPluginsByNavigationAndRegionMethod(33, "left"));
    }
    
    
    public function testExecute(){
        $expected = array(
            array(1,2,3),
            array(1,2,3),
            array(1,2,3)
        );
        $model = $this->getNavigationPluginModelMock(array("findAllByNavigationAndRegion"));
        $model->expects($this->exactly(1))
                ->method("findAllByNavigationAndRegion")
                ->with(3,4)
                ->will($this->returnValue($expected));
        
        $resolver = $this->getResolver();
        $resolver->setNavigationPluginModel($model);
        
        $this->assertEquals($expected, $resolver->execute("tests", array('getRegionExtensionPlugins'), array(3,4)));
    }
    
}

