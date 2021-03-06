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
// Call Lunette_Package_TransactionTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_TransactionTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/Transaction.php';

/**
 * Test class for Lunette_Package_Transaction.
 * Generated by PHPUnit on 2008-06-24 at 20:16:31.
 */
class Lunette_Package_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Package_Transaction
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_TransactionTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Lunette_Package_Transaction;
    }

    /**
     * Tests the 'add' method
     */
    public function testAdd()
    {
        $pkg = $this->getMock('Lunette_Package_Interface');
        $entry = new Lunette_Package_Transaction_Entry($pkg, 'i');
        $this->assertEquals(0, $this->object->count());
        $return = $this->object->add($entry);
        $this->assertEquals(1, $this->object->count());
        $this->assertTrue($return);
        $return2 = $this->object->add($entry);
        $this->assertFalse($return2);
        $this->assertEquals(1, $this->object->count());
    }
    
    /**
     * Tests the 'add' method with a wrong value
     */
    public function testAddInvalid()
    {
        $this->setExpectedException('Lunette_Package_Transaction_Exception', 'This set only allows Lunette_Package_Transaction_Entry objects');
        $this->object->add('abc123');
    }

    /**
     * Tests the 'installPackage' method
     */
    public function testInstallPackage()
    {
        $pkg = $this->getMock('Lunette_Package_Interface');
        $this->assertEquals(0, $this->object->count());
        $this->object->installPackage($pkg);
        $this->assertEquals(1, $this->object->count());
        $array = $this->object->toArray();
        $this->assertTrue($array[0]->isInstall());
    }

    /**
     * Tests the 'removePackage' method
     */
    public function testRemovePackage()
    {
        $pkg = $this->getMock('Lunette_Package_Interface');
        $this->assertEquals(0, $this->object->count());
        $this->object->removePackage($pkg);
        $this->assertEquals(1, $this->object->count());
        $array = $this->object->toArray();
        $this->assertTrue($array[0]->isRemove());
    }

    /**
     * Tests the 'removePackage' method with the purge parameter
     */
    public function testPurgePackage()
    {
        $pkg = $this->getMock('Lunette_Package_Interface');
        $this->assertEquals(0, $this->object->count());
        $this->object->removePackage($pkg, true);
        $this->assertEquals(1, $this->object->count());
        $array = $this->object->toArray();
        $this->assertTrue($array[0]->isPurge());
    }
}

// Call Lunette_Package_TransactionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_TransactionTest::main') {
    Lunette_Package_TransactionTest::main();
}
