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
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_AllTests::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/CachedTest.php';
require_once 'Lunette/Package/MetaTest.php';
require_once 'Lunette/Package/RelationTest.php';
require_once 'Lunette/Package/Relation/TypeTest.php';
require_once 'Lunette/Package/Relation/SetTest.php';
require_once 'Lunette/Package/StateTest.php';
require_once 'Lunette/Package/TransactionTest.php';
require_once 'Lunette/Package/Transaction/EntryTest.php';

/**
 * The suite of tests for Lunette_Package
 *
 */
class Lunette_Package_AllTests extends PHPUnit_Framework_TestSuite
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
        $suite = new self('Lunette Platform - Lunette_Package');
        $suite->addTestSuite('Lunette_Package_CachedTest');
        $suite->addTestSuite('Lunette_Package_MetaTest');
        $suite->addTestSuite('Lunette_Package_RelationTest');
        $suite->addTestSuite('Lunette_Package_Relation_TypeTest');
        $suite->addTestSuite('Lunette_Package_Relation_SetTest');
        $suite->addTestSuite('Lunette_Package_StateTest');
        $suite->addTestSuite('Lunette_Package_TransactionTest');
        $suite->addTestSuite('Lunette_Package_Transaction_EntryTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Lunette_Package_AllTests::main') {
    Lunette_Package_AllTests::main();
}
