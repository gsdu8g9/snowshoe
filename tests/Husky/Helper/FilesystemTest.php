<?php
/**
 *
 * @author Ed van Beinum <e@edvanbeinum.com>
 * @version $Id$
 * @copyright Ibuildings 07/08/2011
 * @package HuskyTest
 */

require_once dirname(__FILE__) . '/../../../Husky/bootstrap.php';
require_once 'vfsStream/vfsStream.php';

/**
 * Test class for FileSystem.
 * Generated by PHPUnit on 2011-08-07 at 21:53:52.
 */
class FileSystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Husky\Helper\FileSystem
     */
    protected $_fileSystem;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_fileSystem = new \Husky\Helper\FileSystem;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->_fileSystem);
    }

    /**
     * createFile() creates new file
     *
     * @test
     */
    public function createFile_creates_new_file()
    {
        $this->_fileSystem->createFile(vfsStream::url('testDir/one.html'), 'some content');
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('one.html'));
    }


    /**
     * createFile() overwrites existing file
     *
     * @test
     */
    public function createFile_overwrites_existing_file()
    {
        vfsStream::newFile('two.html')->at(vfsStreamWrapper::getRoot());
        $this->_fileSystem->createFile(vfsStream::url('testDir/two.html'), 'some content');
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('two.html'));
    }

    /**
     * createFile() creates new file in subdir
     *
     * @test
     */
    public function createFile_creates_new_file_in_subdir()
    {
        $this->_fileSystem->createFile(vfsStream::url('testDir/subDir/three.html'), 'some content');
        $this->assertTrue(vfsStreamWrapper::getroot()->getChild('subDir')->hasChild('three.html'));
    }

    /**
     * CreateDirectory() creates a new directory
     *
     * @test
     */
    public function createDirectory_creates_new_directory()
    {
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('newDir'));

        $this->assertTrue($this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir')));
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('newDir'));
    }

    /**
     * createDirectory returns true with existing directory
     *
     * @test
     */
    public function createDirectoryreturns_true_with_existing_directory()
    {
        vfsStream::newDirectory('newDir', 0755)->at(vfsStreamWrapper::getRoot());
        $this->assertTrue($this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir')));
    }

    /**
     * createDirectory throws exception if directory is unwritable
     *
     * @expectedException Exception
     * @test
     */
    public function createDirectory_throws_exception_if_directory_is_unwritable()
    {
        vfsStreamWrapper::getRoot()->chmod(0400);
        $this->_fileSystem->createDirectory(vfsStream::url('testDir/newDir'));
    }

    /**
     * @return void
     * @test
     */
    public function getSubDirectories_returns_array_of_subdirs()
    {

        $dirStructure = array('base' => array('subOne' => array(), 'subTwo' => array(), 'fileOne' => 'test content'));
        vfsStream::create($dirStructure, 'testDir');

        // since vfsStream is a wrapper around a stream, the returned directories will be prepended with 'vfs://'
        $expectedResult = array('vfs://testDir/base', 'vfs://testDir/base/subOne', 'vfs://testDir/base/subTwo');
        $this->assertSame($expectedResult, $this->_fileSystem->getSubDirectories(vfsStream::url('testDir')));
    }

    /**
     * @return void
     * @test
     */
    public function getSubDirectories_returns_empty_when_passed_only_files()
    {
        $dirStructure = array(
            'fileOne' => 'some content',
            'fileTwo' => 'some content',
            'fileThree' => 'some content'
        );
        vfsStream::create($dirStructure, 'testDir');

        $this->assertEmpty($this->_fileSystem->getSubDirectories(vfsStream::url('testDir')));
    }

    /**
     * @return void
     * @test
     */
    public function getFilesInDirectory_returns_array_of_filenames()
    {
        $dirStructure = array(
            'fileOne.html' => 'some content',
            'fileTwo.html' => 'some content',
            'fileThree.txt' => 'some content'
        );
        vfsStream::create($dirStructure, 'testDir');

        $returnedArray = $this->_fileSystem->getFilesInDirectory(vfsStream::url('testDir'));

        $this->assertInstanceOf(
            'splFileInfo',
            $returnedArray[0],
            'getFilesInDirectory() not returning expected array of object of type: splFileInfo'
        );

        // Filesystem::getFilesInDirectory() returns and array of splFileInfo objects so we create a new array of just filenames
        $fileArray = array();
        foreach ($returnedArray as $fileInfo) {
            $fileArray[] = $fileInfo->getFilename();
        }

        $expected = array(
            'fileOne.html',
            'fileTwo.html'
        );


        $this->assertSame($expected, $fileArray, 'array of filenames not retruned as expected');
    }

    /**
     * @return void
     * @test
     */
    public function getFilesInDirectory_returns_array_of_filenames_with_given_extension()
    {
        $dirStructure = array(
            'fileOne.txt' => 'some content',
            'fileTwo.html' => 'some content',
            'fileThree.txt' => 'some content'
        );
        vfsStream::create($dirStructure, 'testDir');

        $returnedArray = $this->_fileSystem->getFilesInDirectory(vfsStream::url('testDir'), 'txt');

        $this->assertInstanceOf(
            'splFileInfo',
            $returnedArray[0],
            'getFilesInDirectory() not returning expected array of object of type: splFileInfo'
        );

        // Filesystem::getFilesInDirectory() returns and array of splFileInfo objects so we create a new array of just filenames
        $fileArray = array();
        foreach ($returnedArray as $fileInfo) {
            $fileArray[] = $fileInfo->getFilename();
        }

        $expected = array(
            'fileOne.txt',
            'fileThree.txt'
        );


        $this->assertSame($expected, $fileArray, 'array of filenames not retruned as expected');
    }

    /**
     * @return void
     * @test
     */
    public function getFilesInDirectory_returns_empty_when_passed_only_files()
    {
        $dirStructure = array(
            'fileOne' => array(),
            'fileTwo' => array(),
            'fileThree' => array()
        );
        vfsStream::create($dirStructure, 'testDir');

        $this->assertEmpty($this->_fileSystem->getFilesInDirectory(vfsStream::url('testDir')));
    }
}

?>