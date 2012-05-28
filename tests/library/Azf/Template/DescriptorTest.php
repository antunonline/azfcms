<?php

/**
 * /usr/share/php/vfsStream/vfsStream.php
 * /usr/share/php should be in include_path php.ini variable
 */
require 'vfsStream/vfsStream.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DescriptorTest
 *
 * @author antun
 */
class Azf_Template_DescriptorTest extends PHPUnit_Framework_TestCase {
    
    public function getClassPath(){
        return dirname(__FILE__);
    }
    
    public function getResourcesPath() {
        return $this->getClassPath().DIRECTORY_SEPARATOR."resources";
    }
    
    public function getResource($resource){
        return file_get_contents($this->getResourcesPath().DIRECTORY_SEPARATOR.$resource);
    }
    
    protected function setUp() {
        parent::setUp();
        $descriptor = new Azf_Template_Descriptor();
        $schemaSource =  $descriptor->getSchemaSource();
        
        $structure = array(
            'templates'=>array(
                'ValidTemplate.xml'=>$this->getResource("ValidTemplate.xml"),
                'Another Valid Template.XML'=>$this->getResource("Another Valid Template.xml"),
                'Invalid Template.xml'=>$this->getResource("Invalid Template.xml")
            ),
            'classpath'=>array(
                'template.xsd'=>$schemaSource
            )
        );
        
        vfsStream::setup('root',null,$structure);
        
    }
    
    public function getInstance(){
        return new Azf_Template_Descriptor();
    }
    
    public function getFixedInstance(){
        $instance = $this->getInstance();
        $instance->setTemplateDirectoryPath(vfsStream::url("root/templates"));
        return $instance;
    }
    
    public function testGetSchemaSource(){
        $expected = file_get_contents($this->getClassPath()."/../../../../library/Azf/Template/template.xsd");
        $actual = $this->getInstance()->getSchemaSource();
        
        $this->assertEquals($expected,$actual);
    }
    
    public function testGetSchemaSourceWithvfsStreamClasspath(){
        $expected = file_get_contents($this->getClassPath()."/../../../../library/Azf/Template/template.xsd");
        $instance = $this->getInstance();
        
        
        $instance->setClassPath(vfsStream::url("root/classpath")."/class.php");
        $actual = $instance->getSchemaSource();
        
        $this->assertEquals($expected,$actual);
    }
    
    public function testSetTemplates(){
        $templates = array(
            array(
                'name'=>'Valid Template',
                'description'=>"Valid Template Description",
                'identifier'=>'validTemplate',
                'regions'=>array(
                    array('identifier'=>'header','name'=>'Header Region')
                )
            )
        );
        $instance = $this->getInstance();
        $instance->setTemplates($templates);
        
        $this->assertEquals($templates, $instance->getTemplates());
    }
    
    public function testGetTemplateDirectoryPath(){
        $expected = APPLICATION_PATH."/configs/descriptor/template";
        $actual = $this->getInstance()->getTemplateDirectoryPath();
        $this->assertEquals($expected, $actual);
    }
    
    public function testSetTemplateDirectoryPath(){
        $expected = "/some/directory/path";
        $instance = $this->getInstance();
        $instance->setTemplateDirectoryPath($expected);
        $this->assertEquals($expected, $instance->getTemplateDirectoryPath());
    }
    
    public function testGetTemplates(){
        $instance = $this->getInstance();
        $instance->setTemplateDirectoryPath(vfsStream::url("root/templates"));
        
        $this->assertEquals(2, count($instance->getTemplates()));
    }
    
    public function testGetTemplateCase1(){
        $expected = array(
            'name'=>'Valid Template',
            'description'=>'Valid Template Description',
            'identifier'=>'validTemplate',
            'regions'=>array(
                array('identifier'=>'header','name'=>'Header Region')
            )
        );
        
        $actual = $this->getFixedInstance()->getTemplate("validTemplate");
        
        
        $this->assertEquals($expected, $actual);
        
    }
    
    public function testGetTemplateCase2(){
        $expected = array(
            'name'=>'Another Template',
            'description'=>'Another Template Description',
            'identifier'=>'anotherTemplate',
            'regions'=>array(
                array(
                    'name'=>'Left Region',
                    'identifier'=>'leftRegion'
                ),
                array(
                    'name'=>'Right Region',
                    'identifier'=>'rightRegion'
                )
            )
        );
        
        $actual = $this->getFixedInstance()->getTemplate("anotherTemplate");
        
        
        $this->assertEquals($expected, $actual);
        
    }
    
    
    
    
}
