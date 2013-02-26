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
// Call Lunette_File_Reader_Bz2Test::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_File_Reader_Bz2Test::main');
}
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/File/Reader/Bz2.php';

/**
 * Test class for Lunette_File_Reader_Bz2.
 * Generated by PHPUnit on 2008-06-03 at 19:43:10.
 */
class Lunette_File_Reader_Bz2Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_File_Reader_Bz2
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_File_Reader_Bz2Test');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Lunette_File_Reader_Bz2(dirname(__FILE__) . '/_files/example3.txt.bz2');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->object = null;
    }

    /**
     * Tests the 'read' method
     */
    public function testRead()
    {
        $this->object->skip(88);
        $read = $this->object->read(20);
        $this->assertEquals('Down the Rabbit Hole', $read);
    }

    /**
     * Tests reading the entire stream
     */
    public function testReadAll()
    {
        $read = $this->object->read();
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/_files/example3.txt'), $read);
    }
    
    /**
     * Tests the 'rewind' method
     */
    public function testRewind()
    {
        $this->object->skip(10);
        $this->assertEquals(10, $this->object->tell());
        $this->object->rewind();
        $this->assertEquals(0, $this->object->tell());
    }

    /**
     * Tests the 'stat' method
     */
    public function testStat()
    {
        $stat = $this->object->stat();
        $this->assertType('array', $stat);
        $this->assertEquals(stat(dirname(__FILE__) . '/_files/example3.txt.bz2'), $stat);
    }
    
    /**
     * Tests the 'skip' and 'tell' methods 
     */
    public function testSkipAndTell()
    {
        $this->assertEquals(0, $this->object->tell());
        $this->object->skip(10);
        $this->assertEquals(10, $this->object->tell());
    }
}

// Call Lunette_File_Reader_Bz2Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_File_Reader_Bz2Test::main') {
    Lunette_File_Reader_Bz2Test::main();
}
