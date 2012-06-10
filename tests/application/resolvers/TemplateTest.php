<?php

require_once '../application/resolvers/Template.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TemplateTest
 *
 * @author antun
 */
class Application_Resolver_TemplateTest extends PHPUnit_Framework_TestCase {

    /**
     * 
     * @param type $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getTemplateMock($methods) {
        return $this->getMock("Azf_Template_Descriptor", $methods);
    }

    /**
     * 
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getNavigationModelMock($methods) {
        return $this->getMockBuilder("Azf_Model_Tree_Navigation")
                        ->disableOriginalConstructor()->setMethods($methods)
                        ->getMock();
    }

    /**
     * 
     * @param type $template
     * @return Application_Resolver_Template
     */
    public function getResolver($template) {
        $resolver = new Application_Resolver_Template();
        $resolver->setTemplate($template);
        return $resolver;
    }

    public function testGetTemplateRegionsMethod() {
        $expected = array(
            array('name' => 'd1', 'identifier' => 'd1'),
            array('name' => 'd2', 'identifier' => 'd2')
        );
        $template = $this->getTemplateMock(array('getRegions'));
        $template->expects($this->once())
                ->method("getRegions")
                ->with('demo')->will($this->returnValue($expected));

        $resolver = $this->getResolver($template);
        $actual = $resolver->execute("template", array('getTemplateRegions'), array('demo'));

        $this->assertEquals($expected, $actual);
    }

    public function testGetTemplateRegionsForNavigationMethod() {

        $expected = array(
            array('name' => "Left", 'identifier' => 'left'),
            array('name' => "Right", 'identifier' => 'right')
        );
        $template = $this->getTemplateMock(array('getRegions'));
        $template->expects($this->once())
                ->method('getRegions')
                ->with('greenHouse')
                ->will($this->returnValue($expected));

        $navigationModel = $this->getNavigationModelMock(array('getStaticParam'));
        $navigationModel->expects($this->once())
                ->method('getStaticParam')
                ->with(33, 'templateIdentifier')
                ->will($this->returnValue("greenHouse"));

        $resolver = $this->getResolver($template);
        $resolver->setNavigationModel($navigationModel);
        $actual = $resolver->getTemplateRegionsForNavigationMethod(33);

        $this->assertEquals($actual, $expected);
    }
    public function testGetTemplateRegionsForNavigationMethodCase1() {

        $expected = array(
            array('name' => "Left", 'identifier' => 'left'),
            array('name' => "Right", 'identifier' => 'right')
        );
        $template = $this->getTemplateMock(array('getRegions'));
        $template->expects($this->once())
                ->method('getRegions')
                ->with('blueHouse')
                ->will($this->returnValue($expected));

        $navigationModel = $this->getNavigationModelMock(array('getStaticParam'));
        $navigationModel->expects($this->once())
                ->method('getStaticParam')
                ->with(33, 'templateIdentifier')
                ->will($this->returnValue(null));
        
        Zend_Registry::set("defaultTemplate",'blueHouse');

        $resolver = $this->getResolver($template);
        $resolver->setNavigationModel($navigationModel);
        $actual = $resolver->getTemplateRegionsForNavigationMethod(33);

        $this->assertEquals($actual, $expected);
    }

}

