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
 * @package Lunette
 * @subpackage Tests
 * @version $Id$
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_AllTests::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/ApplicationTest.php';
require_once 'Lunette/Auth/AllTests.php';
require_once 'Lunette/Cache/AllTests.php';
require_once 'Lunette/Config/AllTests.php';
require_once 'Lunette/File/AllTests.php';
require_once 'Lunette/Package/AllTests.php';
require_once 'Lunette/User/ServiceTest.php';
require_once 'Lunette/VersionTest.php';

/**
 * The suite of tests for Lunette_Package
 *
 */
class Lunette_AllTests extends PHPUnit_Framework_TestSuite
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
        $suite = new self('Lunette Platform - Lunette');
        $suite->addTestSuite('Lunette_ApplicationTest');
        $suite->addTest( Lunette_Auth_AllTests::suite() );
        $suite->addTest( Lunette_Cache_AllTests::suite() );
        $suite->addTest( Lunette_Config_AllTests::suite() );
        $suite->addTest( Lunette_File_AllTests::suite() );
        $suite->addTest( Lunette_Package_AllTests::suite() );
        $suite->addTestSuite('Lunette_User_ServiceTest');
        $suite->addTestSuite('Lunette_VersionTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Lunette_AllTests::main') {
    Lunette_AllTests::main();
}
