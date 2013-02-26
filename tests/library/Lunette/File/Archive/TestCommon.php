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
 * @package Lunette_File
 * @subpackage Tests
 * @version $Id$
 */
// Call Lunette_File_Archive_TarTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_File_Archive_TarTest::main');
}
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/File/Sandbox.php';

/**
 * Base class for all archive tests
 */
class Lunette_File_Archive_TestCommon extends PHPUnit_Framework_TestCase
{
    /**
     * @var Lunette_File_Sandbox
     */
    protected $sandbox;

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        unset($this->sandbox);
    }
        
    /**
     * Tests the 'getFileContents' method
     */
    public function testGetFileContents()
    {
        $stat = $this->object->stat();
        $size = $stat['size'];
        $data = $this->object->getFileContents();
        $this->assertEquals($size, strlen($data));
    }
    
    /**
     * Tests the 'ls' method
     */
    public function testLs()
    {
        $list = $this->object->ls();
        $this->assertType('array', $list);
        $mylist = array();
        foreach( $this->object as $key => $filename ) {
            $mylist[] = $filename;
        }
        $this->assertEquals($mylist, $list);
    }

    /**
     * Creates a sandbox
     */
    protected function _createSandbox()
    {
        $this->sandbox = null;
        $this->sandbox = new Lunette_File_Sandbox('ArchiveTest');
    }
    
    /**
     * Gets the sandbox name
     *
     * @return string
     */
    protected function _getSandboxName()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ArchiveTest';
    }
}