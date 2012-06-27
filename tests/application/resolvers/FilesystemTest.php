<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FilesystemTest
 *
 * @author antun
 */
require_once "vfsStream/vfsStream.php";

class Application_Resolver_FilesystemTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @return \Application_Resolver_Filesystem 
     */
    public function getInstance() {
        return new Application_Resolver_Filesystem();
    }

    public function setUpFs() {
        vfsStream::setup("/", "777", array(
            'public' => array(
                'files' => array(
                    'file1.jpg' => '',
                    'file2.jpg' => '',
                    'file3.jpg' => '',
                    'file4.jpg' => '',
                    'directory1' => array(
                        'file5.jpg' => '',
                        'file6.jpg' => ''
                    ),
                    '.file7' => '',
                    '.file8' => '',
                    'directory2' => array(
                        'file9.jpg' => '',
                        'file10.jpg' => '',
                        'file11.jpg' => '',
                        'file12.jpg'
                    ),
                    'directory3' => array(
                        'file13.jpg' => ''
                    )
                ),
                'index.php' => "<?php?>",
                'templates' => array(
                    'default' => array(
                        'main.css' => '*{}'
                    )
                ),
                'js' => array(
                    'lib' => array(
                        'dojo' => array(
                            'dojo.js' => 'dojo={}'
                        ),
                        'dijit' => array(),
                        'dojox' => array(),
                        'azfcms' => array()
                    )
                )
            )
        ));
    }

    public function setUp() {
        $this->setUpFs();
    }

    public function testGetDirectoryIterator() {
        $this->assertInstanceOf("DirectoryIterator", $this->getInstance()->getDirectoryIterator("vfs://public/files"));
    }

    public function testGetBaseDir() {
        $this->assertEquals(realpath(APPLICATION_PATH . "/../public/files"), $this->getInstance()->getBaseDir());
    }

    public function testGetBaseDirForStoredValue() {
        $instance = $this->getInstance();
        $instance->setBaseDir(APPLICATION_PATH);
        $this->assertEquals(APPLICATION_PATH, $instance->getBaseDir());
    }

    public function testConstructRealPathFromValidPath() {

        $expected = "vfs://public/files/directory1/file5.jpg";
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $actual = $instance->constructRealPath("directory1/file5.jpg");

        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromInvalidPath() {

        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $actual = $instance->constructRealPath("../../should-not-access.php");

        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromArray() {
        $expected = "vfs://public/files/directory2/file12.jpg";
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(array(
            'dirname' => 'directory2',
            'name' => 'file12.jpg'
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromArrayWithoutName() {
        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(array(
            'dirname' => 'directory2'
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromArrayWithoutDirname() {
        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(array(
            'name' => 'file12.jpg'
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromArrayWithIllegalName() {
        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(array(
            'name' => '../file12.jpg',
            'dirname' => 'directory2'
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathFromArrayWithIllegalDirname() {
        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(array(
            'name' => 'file12.jpg',
            'dirname' => '../directory2'
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testConstructRealPathWithInvalidInputValue() {
        $expected = false;
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $actual = $instance->constructRealPath(new stdClass());
        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeFilterWithEmptyArrayArg() {
        $expected = array(
            'directory' => true,
            'file' => true,
            'hidden' => true
        );

        $actual = $this->getInstance()->normalizeFilter(array());
        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeFilterWithDirectorySetToFalse() {
        $expected = array(
            'directory' => false,
            'file' => true,
            'hidden' => true
        );

        $actual = $this->getInstance()->normalizeFilter(array('directory' => false));
        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeFilterWithFileSetToFalse() {
        $expected = array(
            'directory' => true,
            'file' => false,
            'hidden' => true
        );

        $actual = $this->getInstance()->normalizeFilter(array('file' => false));
        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeFilterWithHiddenSetToFalse() {
        $expected = array(
            'directory' => true,
            'file' => true,
            'hidden' => false
        );

        $actual = $this->getInstance()->normalizeFilter(array('hidden' => false));
        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeFilterWithAllFieldSetToFalse() {
        $expected = array(
            'directory' => false,
            'file' => false,
            'hidden' => false
        );

        $actual = $this->getInstance()->normalizeFilter(array(
            'directory' => false,
            'file' => false,
            'hidden' => false
                ));
        $this->assertEquals($expected, $actual);
    }

    public function testGetDirectoryFileListForVisibleFilesOnly() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $expected = array(
            array(
                'dirname' => '/',
                'name' => 'file1.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file2.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file3.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file4.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => '.file7',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'file7',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => '.file8',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'file8',
                'size' => 0,
                'permissions' => '0666'
            )
        );
        $actual = $instance->getDirectoryFileList(".", array(
            'directory' => false,
            'file' => true,
            'hidden' => true
                ));

        $this->assertEquals($expected, $actual);
    }

    public function testGetDirectoryFileListForFilesOnly() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $expected = array(
            array(
                'dirname' => '/',
                'name' => 'file1.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file2.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file3.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file4.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            )
        );
        $actual = $instance->getDirectoryFileList(".", array(
            'directory' => false,
            'file' => true,
            'hidden' => false
                ));

        $this->assertEquals($expected, $actual);
    }

    public function testGetDirectoryFileListForNoKindOfFile() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $expected = array(
        );
        $actual = $instance->getDirectoryFileList(".", array(
            'directory' => false,
            'file' => false,
            'hidden' => false
                ));

        $this->assertEquals($expected, $actual);
    }

    public function testGetDirectoryFileListForAllFilesAndDirectories() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $expected = array(
            array(
                'dirname' => '/',
                'name' => 'file1.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file2.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file3.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'file4.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'directory1',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => '',
                'size' => 0,
                'permissions' => '0777'
            ),
            array(
                'dirname' => '/',
                'name' => '.file7',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'file7',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => '.file8',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'file8',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/',
                'name' => 'directory2',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => '',
                'size' => 0,
                'permissions' => '0777'
            ),
            array(
                'dirname' => '/',
                'name' => 'directory3',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => '',
                'size' => 0,
                'permissions' => '0777'
            )
        );
        $actual = $instance->getDirectoryFileList(".", array(
            'directory' => true,
            'file' => true,
            'hidden' => true
                ));

        $this->assertEquals($expected, $actual);
    }

    public function testGetDirectoryFileListForSubdirectory() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");

        $expected = array(
            array(
                'dirname' => '/directory1',
                'name' => 'file5.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            ),
            array(
                'dirname' => '/directory1',
                'name' => 'file6.jpg',
                'date' => filectime("vfs://public/files/file1.jpg"),
                'type' => 'jpg',
                'size' => 0,
                'permissions' => '0666'
            )
        );
        
        $actual = $instance->getDirectoryFileList("directory1", array(
            'directory'=>true,
            'file'=>true,
            'hidden'=>true
        ));
        $this->assertEquals($expected,$actual);
    }

    public function testIsPathSecureForInvalidpath() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $expected = false;
        $actual = $instance->isPathSecure("vfs://public/files/appended/../../path.jpg");

        $this->assertEquals($expected, $actual);
    }

    public function testIsPathSecureForPathOutOfDedicatedDirectory() {
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $expected = false;
        $actual = $instance->isPathSecure("vfs://public/index.php");

        $this->assertEquals($expected, $actual);
    }

    public function testUploadFilesMethodWithOneValidFile() {
        $mock = $this->getMockBuilder("Application_Resolver_Filesystem")
                ->setMethods(array("_isUploadedFile", "_moveUploadedFile"))
                ->getMock();
        $mock->expects($this->once())
                ->method("_isUploadedFile")
                ->with("vfs://tmp/uploadedFile1.jpg")
                ->will($this->returnValue(true));
        $mock->expects($this->once())
                ->method("_moveUploadedFile")
                ->with("vfs://tmp/uploadedFile1.jpg", "vfs://public/files/newFile1.jpg")
                ->will($this->returnCallback(function($from, $to) {
                                    rename($from, $to);
                                }));

        vfsStream::create(array(
            'tmp' => array(
                'uploadedFile1.jpg' => "JPG"
            )
        ));

        $_FILES = array(
            'file1' => array(
                'name' => 'newFile1.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile1.jpg'
            )
        );

        $mock->setBaseDir("vfs://public/files");
        $mock->uploadFilesMethod('.');
        $this->assertFileExists("vfs://public/files/newFile1.jpg");
    }

    public function testUploadFilesMethodWithOneValidFileAndDirectoryAsFileArray() {
        $mock = $this->getMockBuilder("Application_Resolver_Filesystem")
                ->setMethods(array("_isUploadedFile", "_moveUploadedFile"))
                ->getMock();
        $mock->expects($this->once())
                ->method("_isUploadedFile")
                ->with("vfs://tmp/uploadedFile1.jpg")
                ->will($this->returnValue(true));
        $mock->expects($this->once())
                ->method("_moveUploadedFile")
                ->with("vfs://tmp/uploadedFile1.jpg", "vfs://public/files/newFile1.jpg")
                ->will($this->returnCallback(function($from, $to) {
                                    rename($from, $to);
                                }));

        vfsStream::create(array(
            'tmp' => array(
                'uploadedFile1.jpg' => "JPG"
            )
        ));

        $_FILES = array(
            'file1' => array(
                'name' => 'newFile1.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile1.jpg'
            )
        );

        $mock->setBaseDir("vfs://public/files");
        $mock->uploadFilesMethod(array(
            'dirname' => '/',
            'name' => '.'
        ));
        $this->assertFileExists("vfs://public/files/newFile1.jpg");
    }

    public function testUploadFilesMethodWithTwoValidFilesAndDirectoryAsFileArray() {
        $mock = $this->getMockBuilder("Application_Resolver_Filesystem")
                ->setMethods(array("_isUploadedFile", "_moveUploadedFile"))
                ->getMock();
        $mock->expects($this->exactly(2))
                ->method("_isUploadedFile")
                ->will($this->returnValue(true));
        $mock->expects($this->exactly(2))
                ->method("_moveUploadedFile")
                ->will($this->returnCallback(function($from, $to) {
                                    rename($from, $to);
                                }));

        vfsStream::create(array(
            'tmp' => array(
                'uploadedFile1.jpg' => "JPG1",
                'uploadedFile2.jpg' => 'JPG2'
            )
        ));

        $_FILES = array(
            'file1' => array(
                'name' => 'newFile1.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile1.jpg'
            ),
            'file2' => array(
                'name' => 'newFile2.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile2.jpg'
            )
        );

        $mock->setBaseDir("vfs://public/files");
        $mock->uploadFilesMethod(array(
            'dirname' => '/',
            'name' => '.'
        ));
        $this->assertFileExists("vfs://public/files/newFile1.jpg");
        $this->assertFileExists("vfs://public/files/newFile2.jpg");
    }

    public function testUploadFilesMethodWithOneValidAndOneInvalidFileIntoDirectoryAsFileArray() {
        $mock = $this->getMockBuilder("Application_Resolver_Filesystem")
                ->setMethods(array("_isUploadedFile", "_moveUploadedFile"))
                ->getMock();
        $mock->expects($this->at(0))
                ->method("_isUploadedFile")
                ->will($this->returnValue(true));
        $mock->expects($this->at(1))
                ->method("_isUploadedFile")
                ->will($this->returnValue(false));
        $mock->expects($this->exactly(1))
                ->method("_moveUploadedFile")
                ->will($this->returnCallback(function($from, $to) {
                                    rename($from, $to);
                                }));

        vfsStream::create(array(
            'tmp' => array(
                'uploadedFile1.jpg' => "JPG1",
                'uploadedFile2.jpg' => 'JPG2'
            )
        ));

        $_FILES = array(
            'file1' => array(
                'name' => 'newFile1.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile1.jpg'
            ),
            'file2' => array(
                'name' => 'newFile2.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile2.jpg'
            )
        );

        $mock->setBaseDir("vfs://public/files");
        $mock->uploadFilesMethod(array(
            'dirname' => '/',
            'name' => '.'
        ));
        $this->assertFileExists("vfs://public/files/newFile1.jpg");
        $this->assertFileNotExists("vfs://public/files/newFile2.jpg");
    }

    public function testUploadFilesMethodWithOneValidAndOneInvalidFileIntoSubidrectoryAsFileArray() {
        $mock = $this->getMockBuilder("Application_Resolver_Filesystem")
                ->setMethods(array("_isUploadedFile", "_moveUploadedFile"))
                ->getMock();
        $mock->expects($this->at(0))
                ->method("_isUploadedFile")
                ->will($this->returnValue(true));
        $mock->expects($this->exactly(1))
                ->method("_moveUploadedFile")
                ->with("vfs://tmp/uploadedFile1.jpg", 'vfs://public/files/directory1/newFile1.jpg')
                ->will($this->returnCallback(function($from, $to) {
                                    rename($from, $to);
                                }));

        vfsStream::create(array(
            'tmp' => array(
                'uploadedFile1.jpg' => "JPG1",
            )
        ));

        $_FILES = array(
            'file1' => array(
                'name' => 'newFile1.jpg',
                'tmp_name' => 'vfs://tmp/uploadedFile1.jpg'
            )
        );

        $mock->setBaseDir("vfs://public/files");
        $mock->uploadFilesMethod(array(
            'dirname' => '/',
            'name' => 'directory1'
        ));
        $this->assertFileExists("vfs://public/files/directory1/newFile1.jpg");
    }
    
    
    public function testDeleteFilesMethodForTwoExistingFiles(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        
        
        $instance->deleteFilesMethod(array(
            array('dirname'=>'/','name'=>'file1.jpg'),
            array('dirname'=>'.','name'=>'file2.jpg')
        ));
        
        $this->assertFileNotExists("vfs://public/files/file1.jpg");
        $this->assertFileNotExists("vfs://public/files/file2.jpg");
        $this->assertFileExists("vfs://public/files/file3.jpg");
    }
    
    
    public function testDeleteFilesMethodForOneExistingAndOneNonexistingFile(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        
        
        $instance->deleteFilesMethod(array(
            array('dirname'=>'/','name'=>'file1.jpg'),
            array('dirname'=>'.','name'=>'filexx.jpg')
        ));
        
        $this->assertFileNotExists("vfs://public/files/file1.jpg");
        $this->assertFileNotExists("vfs://public/files/filexx.jpg");
    }
    
    
    public function testDeleteFileMethodForInvalidFileStructure(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        
        
        $instance->deleteFilesMethod(array(
            array('name'=>'file1.jpg'),
            array('name'=>'file2.jpg')
        ));
        
        $this->assertFileExists("vfs://public/files/file1.jpg");
        $this->assertFileExists("vfs://public/files/file2.jpg");
    }
    
    
    public function testCreateDirectoryMethodWithOneLevelDirectory(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $instance->createDirectoryMethod("directoryXY");
        
        $this->assertFileExists("vfs://public/files/directoryXY");
    }
    
    
    public function testCreateDirectoryMethodWithThreeLevelDirectory(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $instance->createDirectoryMethod("directoryXY/directory1/directory2");
        
        $this->assertFileExists("vfs://public/files/directoryXY/directory1/directory2");
    }
    
    
    public function testCreateDirectoryInIllegalFolder(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $instance->createDirectoryMethod("../directoryXY");
        $this->assertFileNotExists("vfs://public/directoryXY");
    }
    
    public function testCreateDirectoryInIllegalFolderTry1(){
        $instance = $this->getInstance();
        $instance->setBaseDir("vfs://public/files");
        $instance->createDirectoryMethod("../directoryXY/directory1/directory2");
        $this->assertFileNotExists("vfs://public/directoryXY/directory1/directory2");
    }

}

