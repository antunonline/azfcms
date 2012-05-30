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
class Application_Resolver_TemplateTest extends PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @param type $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getTemplateMock($methods){
        return $this->getMock("Azf_Template_Descriptor",$methods);
    }
    
    /**
     * 
     * @param type $template
     * @return Application_Resolver_Template
     */
    public function getResolver($template){
        $resolver = new Application_Resolver_Template();
        $resolver->setTemplate($template);
        return $resolver;
    }
    
    public function testGetTemplateRegionsMethod(){
        $expected = array(
            array('name'=>'d1','identifier'=>'d1'),
            array('name'=>'d2','identifier'=>'d2')
        );
        $template = $this->getTemplateMock(array('getRegions'));
        $template->expects($this->once())
                ->method("getRegions")
                ->with('demo')->will($this->returnValue($expected));
        
        $resolver = $this->getResolver($template);
        $actual = $resolver->execute("template", array('getTemplateRegions'), array('demo'));
        
        $this->assertEquals($expected,$actual);
    }
}

