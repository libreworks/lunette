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
 * @package Lunette_Cache
 * @subpackage Tests
 * @version $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Cache_AllTests::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/TestDbSetup.php';
require_once 'Lunette/Cache/ServiceTest.php';
require_once 'Lunette/Orm/Mapper.php';
require_once 'Xyster/Orm/Loader.php';
require_once 'Xyster/Orm.php';
/**
 * The suite of tests for Lunette_Cache
 *
 */
class Lunette_Cache_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Executes the suite
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    
    /**
     * Gets the test suite
     *
     * @return Lunette_Cache_AllTests
     */
    public static function suite()
    {
        $suite = new self('Lunette Platform - Lunette_Cache');
        $suite->addTestSuite('Lunette_Cache_ServiceTest');
        return $suite;
    }
    
    /**
     * Sets up the test suite
     */
    protected function setUp()
    {
        $setup = new Lunette_TestDbSetup;
        $setup->setupCache();
        Lunette_Orm_Mapper::dsn('lunette', $setup->getDbAdapter());
        Xyster_Orm_Loader::addPath(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/src/application/orm');
        $this->sharedFixture = Xyster_Orm::getInstance();
    }
    
    /**
     * Tears down the test suite
     */
    protected function tearDown()
    {
        $setup = new Lunette_TestDbSetup;
        $setup->tearDownCache();
    }
}


if (PHPUnit_MAIN_METHOD == 'Lunette_Cache_AllTests::main') {
    Lunette_Cache_AllTests::main();
}
