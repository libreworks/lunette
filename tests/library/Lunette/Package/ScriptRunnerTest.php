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
// Call Lunette_Package_ScriptRunnerTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_ScriptRunnerTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/ScriptRunner.php';
require_once 'Lunette/Package/Cached.php';
require_once 'Lunette/Application.php';
require_once 'Lunette/TestDbSetup.php';

/**
 * Test class for Lunette_Package_ScriptRunner.
 * Generated by PHPUnit on 2008-06-28 at 13:30:23.
 */
class Lunette_Package_ScriptRunnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Package_ScriptRunner
     */
    protected $object;

    protected $app;
    
    protected $pkg;
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_ScriptRunnerTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $setup = new Lunette_TestDbSetup;
        $setup->setupPackage();
        $package = new LunettePackage;
        $package->name = 'foobar-package';
        $package->version = '2.0.2-lunette5';
        $package->state = 5;
        $package->installedSize = 3123.58;
        $package->essential = 'yes';
        $package->depends = 'food, water, shelter (>= 2.0)';
        $package->provides = 'foo, bar';
        $package->conflicts = 'foo, bar';
        $package->preinst = 'echo "Hello, world!"; return 0;';
        $package->postinst = 'echo "Error occurred"; return 1;';
        $package->prerm = 'throw new Exception("An error occurred");';
        $package->postrm = 'echo "Hiya"; return 0;';
        $this->pkg = new Lunette_Package_Cached($package);
        
        $this->app = new Lunette_Application();
        $this->object = new Lunette_Package_ScriptRunner($this->app, $this->pkg);
    }

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        $setup = new Lunette_TestDbSetup;
        $setup->tearDownPackage();
    }

    /**
     * Tests the 'preinst' method
     */
    public function testPreinst()
    {
        $result = $this->object->preinst(array('install'));
        $this->assertSame(0, $result);
        $this->assertNull($this->object->getError());
    }

    /**
     * Tests the 'postinst' method
     */
    public function testPostinst()
    {
        $result = $this->object->postinst(array('install'));
        $this->assertSame(1, $result);
        $this->assertEquals('Error occurred', $this->object->getError());
    }

    /**
     * Tests the 'prerm' method
     */
    public function testPrerm()
    {
        $result = $this->object->prerm(array('remove'));
        $this->assertSame(1, $result);
        $this->assertEquals('An error occurred', $this->object->getError());
    }

    /**
     * Tests the 'postrm' method
     */
    public function testPostrm()
    {
        $result = $this->object->postrm(array('remove'));
        $this->assertSame(0, $result);
        $this->assertNull($this->object->getError());
    }
}

// Call Lunette_Package_ScriptRunnerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_ScriptRunnerTest::main') {
    Lunette_Package_ScriptRunnerTest::main();
}
