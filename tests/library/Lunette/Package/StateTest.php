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
// Call Lunette_Package_StateTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_StateTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/State.php';

/**
 * Test class for Lunette_Package_State.
 * Generated by PHPUnit on 2008-06-07 at 13:49:28.
 */
class Lunette_Package_StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_StateTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Tests the 'NotInstalled' method
     */
    public function testNotInstalled()
    {
       $this->_runTests(Lunette_Package_State::NotInstalled(), 'NotInstalled', 0);
    }
    
    /**
     * Tests the 'UnPacked' method
     */
    public function testUnPacked()
    {
       $this->_runTests(Lunette_Package_State::UnPacked(), 'UnPacked', 1);
    }
    
    /**
     * Tests the 'HalfConfigured' method
     */
    public function testHalfConfigured()
    {
        $this->_runTests(Lunette_Package_State::HalfConfigured(), 'HalfConfigured', 2);
    }
    
    /**
     * Tests the 'HalfInstalled' method
     */
    public function testHalfInstalled()
    {
       $this->_runTests(Lunette_Package_State::HalfInstalled(), 'HalfInstalled', 3);
    }
    
    /**
     * Tests the 'ConfigFiles' method
     */
    public function testConfigFiles()
    {
       $this->_runTests(Lunette_Package_State::ConfigFiles(), 'ConfigFiles', 4);
    }
    
    /**
     * Tests the 'Installed' method
     */
    public function testInstalled()
    {
       $this->_runTests(Lunette_Package_State::Installed(), 'Installed', 5);
    }

    /**
     * Run the common tests
     *
     * @param Lunette_Package_State $actual
     * @param string $name
     * @param int $value
     */
    protected function _runTests( $actual, $name, $value )
    {
        $this->assertEquals($name, $actual->getName());
        $this->assertEquals($value, $actual->getValue());
        $this->assertEquals('Lunette_Package_State ['.$value.','.$name.']', (string)$actual);
        $this->assertEquals($actual,Xyster_Enum::parse('Lunette_Package_State', $name));
        $this->assertEquals($actual,Xyster_Enum::valueOf('Lunette_Package_State', $value));
    }
}

// Call Lunette_Package_StateTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_StateTest::main') {
    Lunette_Package_StateTest::main();
}
