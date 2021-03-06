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
 * @package Lunette_Application
 * @subpackage Tests
 * @version $Id$
 */
// Call Lunette_ApplicationTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_ApplicationTest::main');
}
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Application.php';

/**
 * Test class for Lunette_Application.
 * Generated by PHPUnit on 2008-05-29 at 11:11:26.
 */
class Lunette_ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Lunette_Application
     */
    protected $object;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_ApplicationTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Lunette_ApplicationTest_Application;
    }

    /**
     * Tests the 'getDatabaseAdapter' method
     *
     */
    public function testGetDatabaseAdapter()
    {
        $adapter = $this->object->getDatabaseAdapter();
        $this->assertType('Zend_Db_Adapter_Abstract', $adapter);
        $this->assertSame($adapter, Zend_Registry::get(md5('lunette')));
    }
    
    /**
     * Tests the 'getSystemConfig' method
     */
    public function testGetSystemConfig()
    {
        $config = $this->object->getSystemConfig();
        $this->assertType('Zend_Config', $config);
    }

    /**
     * Tests the 'getSystemConfig' method with a bad configuration file
     */
    public function testGetSystemConfigBad()
    {
        $this->object->mode = 'bad';
        $this->setExpectedException('Lunette_Config_Exception');
        $this->object->getSystemConfig();
    }

    /**
     * Tests the 'getSystemConfig' method with a missing configuration file
     */
    public function testGetSystemConfigGone()
    {
        $this->object->mode = 'gone';
        $this->setExpectedException('Lunette_Config_Exception', 'Missing or unreadable configuration file');
        $this->object->getSystemConfig();
    }
    
    /**
     * Tests the 'getApplicationPath' method
     */
    public function testGetApplicationPath()
    {
        $this->assertEquals(dirname(dirname(dirname(dirname(__FILE__)))) .
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'application',
            $this->object->getApplicationPath());
    }
}

class Lunette_ApplicationTest_Application extends Lunette_Application
{
    public $mode = 'good';
    
    protected function _getConfigurationFile()
    {
        $directory = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' .
                DIRECTORY_SEPARATOR; 
        if ( $this->mode == 'good' ) {
            return  $directory. 'config.ini.php';
        } else if ( $this->mode == 'bad' ) {
            return $directory . 'config-bad.ini.php';
        } else if ( $this->mode == 'gone' ) {
            return $directory . 'not-there';
        }
    }
}

// Call Lunette_ApplicationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_ApplicationTest::main') {
    Lunette_ApplicationTest::main();
}
