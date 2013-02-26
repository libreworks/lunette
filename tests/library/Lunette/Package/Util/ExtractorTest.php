<?php
/**
 * Lunette Platform
 * 
 * Lunette is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * Lunette is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Lunette. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 * @subpackage Tests
 * @version $Id$
 */
// Call Lunette_Package_Util_ExtractorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_Util_ExtractorTest::main');
}
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/Util/Extractor.php';
require_once 'Lunette/File/Sandbox.php';
require_once 'Lunette/Package/Interface.php';
require_once 'Lunette/Package/Relation/Type.php';
require_once 'Lunette/Application.php';
require_once 'Lunette/Package/Service.php';

/**
 * Test class for Lunette_Package_Util_Extractor.
 * Generated by PHPUnit on 2008-06-29 at 22:46:57.
 */
class Lunette_Package_Util_ExtractorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Package_Util_Extractor
     */
    protected $object;
    
    /**
     * @var Lunette_File_Sandbox
     */
    protected $sandbox;
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_Util_ExtractorTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->sandbox = new Lunette_File_Sandbox('PackageUtilExtractorTest');
        $this->object = new Lunette_Package_Util_Extractor($this->sandbox->getRealpath());
    }

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        $this->sandbox = null;
    }

    /**
     * Tests the 'backupOld' and 'removeOld' methods
     */
    public function testBackupAndRemoveOld()
    {
        $files = array('test.txt', 'test2.txt', 'test3.txt', 'test4.txt');
        $expected = array();
        foreach( $files as $file ) {
            $filename = $this->sandbox->getRealpath() . '/' . $file;
            file_put_contents($filename, 'test123');
            $expected[] = $filename . '.pkg.tmp';
        }
        
        $old = $this->getMock('Lunette_Package_Interface');
        $old->expects($this->any())->method('getFiles')->will($this->returnValue($files));
        
        $this->object->backupOld($old);
        $this->assertAttributeEquals($expected, '_backs', $this->object);
        
        foreach( $expected as $expectedFile ) {
            $this->assertFileExists($expectedFile);
        }
            
        $this->object->removeOld();
        
        foreach( $expected as $expectedFile ) {
            $this->assertFileNotExists($expectedFile);
        }
    }
    
    /**
     * Tests the 'backupOld' and 'replaceOld' methods
     */
    public function testBackupAndReplaceOld()
    {
        $files = array('test.txt', 'test2.txt', 'test3.txt', 'test4.txt');
        $expected = array();
        foreach( $files as $file ) {
            $filename = $this->sandbox->getRealpath() . '/' . $file;
            file_put_contents($filename, 'test123');
            $expected[] = $filename . '.pkg.tmp';
        }
        
        $old = $this->getMock('Lunette_Package_Interface');
        $old->expects($this->any())->method('getFiles')->will($this->returnValue($files));
        
        $this->object->backupOld($old);
        $this->assertAttributeEquals($expected, '_backs', $this->object);
        
        foreach( $expected as $expectedFile ) {
            $this->assertFileExists($expectedFile);
            file_put_contents(substr($expectedFile, 0, -8).'.new', 'aoeuaoeuaoeu');
            rename(substr($expectedFile, 0, -8).'.new', substr($expectedFile, 0, -8));
        }
        
        $this->object->replaceOld();
        
        foreach( $expected as $expectedFile ) {
            $this->assertFileNotExists($expectedFile);
            $this->assertEquals('test123', file_get_contents(substr($expectedFile, 0, -8)));
        }
    }
    
    /**
     * Tests the 'getFilename' method
     */
    public function testGetFilename()
    {
        $filename = 'aoeuhtns.dvorak';
        $this->assertEquals($this->sandbox->getRealpath() . '/' . $filename . '.pkg.new',
            $this->object->getFilename($filename));
    }

    /**
     * Tests the 'write' method
     */
    public function testWrite()
    {
        $data = 'test data';
        $filename = 'test.txt';
        $stat = array('mtime' => strtotime('-1 month'), 'mode' => 0755, 'size' => strlen($data));
        $realname = $this->sandbox->getRealpath() . '/' . $filename;
        $this->assertFileNotExists($realname);
        $this->assertFileNotExists($realname . '.pkg.new');
        $this->object->write($data, $filename, $stat);
        $this->assertFileExists($realname);
        $this->assertFileNotExists($realname . '.pkg.new');
        $this->assertEquals(strlen($data), filesize($realname));
        $this->assertEquals('0755', substr(sprintf('%o', fileperms($realname)), -4));
        $this->assertEquals($stat['mtime'], filemtime($realname));
    }
}

// Call Lunette_Package_Util_ExtractorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_Util_ExtractorTest::main') {
    Lunette_Package_Util_ExtractorTest::main();
}