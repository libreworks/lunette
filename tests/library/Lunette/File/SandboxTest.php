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
// Call Lunette_File_SandboxTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_File_SandboxTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/File/Sandbox.php';

/**
 * Test class for Lunette_File_Sandbox.
 * Generated by PHPUnit on 2008-06-16 at 16:59:44.
 */
class Lunette_File_SandboxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_File_SandboxTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Tests the basic operation of the class
     */
    public function testBasic()
    {
        $sandbox = new Lunette_File_Sandbox('SandboxTest');
        $this->assertFileExists($sandbox->getRealpath());
        $this->assertEquals('SandboxTest', $sandbox->getNamespace());
        $this->assertEquals(sys_get_temp_dir() . DIRECTORY_SEPARATOR .
            'SandboxTest', $sandbox->getRealpath());
        $iterator = $sandbox->getIterator();
        $this->assertType('DirectoryIterator', $iterator);
        
        $realpath = $sandbox->getRealpath();
        mkdir($realpath . '/test123');
        file_put_contents($realpath . '/test123/aoeuaoeu.txt', 'just a test');
        $this->assertFileExists($realpath . '/test123/aoeuaoeu.txt');
        unset($sandbox);
        $this->assertFileNotExists($realpath . '/test123/aoeuaoeu.txt');
        $this->assertFileNotExists($realpath);
    }
}

// Call Lunette_File_SandboxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_File_SandboxTest::main') {
    Lunette_File_SandboxTest::main();
}
