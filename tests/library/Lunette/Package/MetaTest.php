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
 * @package Lunette_Package
 * @subpackage Tests
 * @version $Id$
 */
// Call Lunette_Package_MetaTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_MetaTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/Meta.php';
require_once 'Lunette/TestDbSetup.php';
require_once 'Lunette/Orm/Mapper.php';
require_once 'Xyster/Orm/Loader.php';
require_once 'Xyster/Orm.php';

/**
 * Test class for Lunette_Package_Meta.
 * Generated by PHPUnit on 2008-06-04 at 11:49:09.
 */
class Lunette_Package_MetaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Package_Meta
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_MetaTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $setup = new Lunette_TestDbSetup;
        $setup->setupPackage();
        Lunette_Orm_Mapper::dsn('lunette', $setup->getDbAdapter());
        Xyster_Orm_Loader::addPath(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/src/application/orm');
        Xyster_Orm::getInstance()->setup('LunettePackage');
        
        $this->object = new Lunette_Package_Meta(dirname(dirname(__FILE__)) . '/File/Archive/_files/unittest-1.0-all.deb');
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
        $setup = new Lunette_TestDbSetup;
        $setup->tearDownPackage();
    }

    /**
     * Tests the 'getRelations' method
     */
    public function testGetRelations()
    {
        foreach( range(0, 6) as $value ) {
            $type = Xyster_Enum::valueOf('Lunette_Package_RelationType', $value);
            $relations = $this->object->getRelations($type);
            $this->assertType('Lunette_Package_Relation_Set', $relations);
            $this->assertSame($relations, $this->object->getRelations($type));
        }
    }

    /**
     * Tests the 'getState' method
     */
    public function testGetState()
    {
        require_once 'Lunette/Package/State.php';
        require_once 'Lunette/Package/Service.php';
        $service = new Lunette_Package_Service(Xyster_Orm::getInstance());
        $this->assertSame(Lunette_Package_State::NotInstalled(), $this->object->getState($service));
    }

    /**
     * Tests the 'toString' method
     */
    public function test__toString()
    {
        $this->assertEquals('lunette-unit-test-example-package 1.0', $this->object->__toString());
    }
}

// Call Lunette_Package_MetaTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_MetaTest::main') {
    Lunette_Package_MetaTest::main();
}
