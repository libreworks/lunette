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
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 * @subpackage Tests
 * @version $Id$
 */
// Call Lunette_File_Archive_ArTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_File_Archive_ArTest::main');
}
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCommon.php';
require_once 'Lunette/File/Archive/Ar.php';

/**
 * Test class for Lunette_File_Archive_Ar.
 * Generated by PHPUnit on 2008-06-02 at 19:06:30.
 */
class Lunette_File_Archive_ArTest extends Lunette_File_Archive_TestCommon
{
    /**
     * @var    Lunette_File_Archive_Ar
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_File_Archive_ArTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Lunette_File_Archive_Ar(dirname(__FILE__) . '/_files/unittest-1.0-all.deb');
    }

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * Tests the constructor with a non-Ar file
     */
    public function testConstructInvalid()
    {
        $this->setExpectedException('Lunette_File_Archive_Exception');
        $object = new Lunette_File_Archive_Ar(dirname(__FILE__) . '/_files/TarTest.tar');
    }

    /**
     * Tests the 'next' method
     */
    public function testNext()
    {
        $delegate = $this->object->getDelegate();
        $tell = $delegate->tell();
        $fname = $this->object->current();
        $stat = $this->object->stat();
        $size = $stat['size'];
        $expected = $tell + $size + 60;
        $this->object->next();
        $this->assertNotEquals($fname, $this->object->current());
        $this->assertEquals($expected, $delegate->tell());
    }

    /**
     * Tests the 'rewind' method
     */
    public function testRewind()
    {
        $delegate = $this->object->getDelegate();
        $this->object->next();
        $this->assertNotEquals(68, $delegate->tell());
        $this->object->rewind();
        $this->assertEquals(68, $delegate->tell());
    }
}

// Call Lunette_File_Archive_ArTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_File_Archive_ArTest::main') {
    Lunette_File_Archive_ArTest::main();
}
