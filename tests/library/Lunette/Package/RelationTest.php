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
// Call Lunette_Package_RelationTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Lunette_Package_RelationTest::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once 'Lunette/Package/Relation.php';
require_once 'Lunette/Package/Interface.php';
require_once 'Lunette/Package/RelationType.php';
require_once 'Lunette/Package/Service.php';
require_once 'Xyster/Data/Operator/Expression.php';
require_once 'Xyster/Orm.php';

/**
 * Test class for Lunette_Package_Relation.
 * Generated by PHPUnit on 2008-06-16 at 12:19:25.
 */
class Lunette_Package_RelationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Lunette_Package_RelationTest_Svc
     */
    protected $svc;
    
    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        $suite  = new PHPUnit_Framework_TestSuite('Lunette_Package_RelationTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->svc = new Lunette_Package_RelationTest_Svc(Xyster_Orm::getInstance());
    }
    
    /**
     * Tests the basic operation of the class
     */
    public function testBasic()
    {
        $parent = $this->getMock('Lunette_Package_Interface');
        $versionFull = 'testpackage (>= 1.0.2)';
        $name = 'testpackage';
        $op = Xyster_Data_Operator_Expression::Gte();
        $version = '1.0.2';
        $type = Lunette_Package_RelationType::Depends();
        $rel = new Lunette_Package_Relation($parent, $versionFull, $type);
        
        $this->assertSame($parent, $rel->getParent());
        $this->assertSame($type, $rel->getType());
        $this->assertSame($op, $rel->getOperator());
        $this->assertEquals($version, $rel->getVersion());
        $this->assertEquals($name, $rel->getName());
        
        $this->svc->installed = '1.0.4';
        $this->assertTrue($rel->isSatisfied($this->svc));
        $this->svc->installed = '1.0.2';
        $this->assertTrue($rel->isSatisfied($this->svc));
        $this->svc->installed = '0.9.0';
        $this->assertFalse($rel->isSatisfied($this->svc));
    }
    
    /**
     * Tests the operation of the class with no version requirement
     */
    public function testNoVersion()
    {
        $parent = $this->getMock('Lunette_Package_Interface');
        $versionFull = 'testpackage';
        $name = 'testpackage';
        $version = null;
        $op = null;
        $type = Lunette_Package_RelationType::Replaces();
        $rel = new Lunette_Package_Relation($parent, $versionFull, $type);
        
        $this->assertSame($parent, $rel->getParent());
        $this->assertSame($type, $rel->getType());
        $this->assertSame($op, $rel->getOperator());
        $this->assertEquals($version, $rel->getVersion());
        $this->assertEquals($name, $rel->getName());
        
        $this->svc->installed = '1.0.4';
        $this->assertTrue($rel->isSatisfied($this->svc));
        $this->svc->installed = '1.0.2';
        $this->assertTrue($rel->isSatisfied($this->svc));
        $this->svc->installed = '0.9.0';
        $this->assertTrue($rel->isSatisfied($this->svc));
    }
}

class Lunette_Package_RelationTest_Svc extends Lunette_Package_Service
{
    public $installed;
    
    public function getInstalledVersion( $name )
    {
        return $this->installed;
    }
}

// Call Lunette_Package_RelationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Lunette_Package_RelationTest::main') {
    Lunette_Package_RelationTest::main();
}
