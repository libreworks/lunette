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
// Call Lunette_Package_Transaction_EntryTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_Transaction_EntryTest::main');
}
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/Transaction/Entry.php';

/**
 * Test class for Lunette_Package_Transaction_Entry.
 * Generated by PHPUnit on 2008-06-24 at 20:05:02.
 */
class Lunette_Package_Transaction_EntryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Package_Interface
     */
    protected $pkg;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_Transaction_EntryTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->pkg = $this->getMock('Lunette_Package_Interface');
    }
    
    /**
     * Tests the 'getPackage' method
     */
    public function testGetPackage()
    {
        $entry = new Lunette_Package_Transaction_Entry($this->pkg, 'i');
        $this->assertSame($this->pkg, $entry->getPackage());
    }

    /**
     * Tests the 'isInstall' method
     */
    public function testIsInstall()
    {
        $entry = new Lunette_Package_Transaction_Entry($this->pkg, 'i');
        $this->assertTrue($entry->isInstall());
        $this->assertFalse($entry->isPurge());
        $this->assertFalse($entry->isRemove());
    }

    /**
     * Tests the 'isPurge' method
     */
    public function testIsPurge()
    {
        $entry = new Lunette_Package_Transaction_Entry($this->pkg, 'p');
        $this->assertFalse($entry->isInstall());
        $this->assertTrue($entry->isPurge());
        $this->assertTrue($entry->isRemove());
    }

    /**
     * Tests the 'isRemove' method
     */
    public function testIsRemove()
    {
        $entry = new Lunette_Package_Transaction_Entry($this->pkg, 'r');
        $this->assertFalse($entry->isInstall());
        $this->assertFalse($entry->isPurge());
        $this->assertTrue($entry->isRemove());
    }
}

// Call Lunette_Package_Transaction_EntryTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_Transaction_EntryTest::main') {
    Lunette_Package_Transaction_EntryTest::main();
}
