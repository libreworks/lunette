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
 * @version $Id$
 */
/**
 * @see Lunette_Package_Relation_Type
 */
require_once 'Lunette/Package/Relation/Type.php';
/**
 * @see Lunette_Package_Relation_Interface
 */
require_once 'Lunette/Package/Relation/Interface.php';
/**
 * Package relation information
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Relation implements Lunette_Package_Relation_Interface
{
    /**
     * @var Lunette_Package_Interface
     */
    protected $_parent;
    
    /**
     * @var string
     */
    protected $_name;
    
    /**
     * @var Xyster_Data_Operator_Expression
     */
    protected $_operator;
    
    /**
     * @var string
     */
    protected $_version;
    
    /**
     * @var Lunette_Package_Relation_Type
     */
    protected $_type;
    
    /**
     * Creates a new package relation
     *
     * @param Lunette_Package_Interface $parent The package with the requirement
     * @param string $requirement The name and version information
     * @param Lunette_Package_Relation_Type $type The type of relation
     */
    public function __construct( Lunette_Package_Interface $parent, $requirement, Lunette_Package_Relation_Type $type )
    {
        $this->_parent = $parent;
        $this->_parseRequirement($requirement);
        $this->_type = $type;
    }
    
    /**
     * Gets the name of the target package
     *
     * @return string The target package name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the operator for version comparison
     * 
     * Only the following operators will be returned,
     * - lte
     * - lt
     * - eq
     * - gt
     * - gte
     * 
     * If there is no version requirement, this method will return null. 
     * 
     * @return Xyster_Data_Operator_Expression or null
     */
    public function getOperator()
    {
        return $this->_operator;
    }
    
    /**
     * Gets the package that owns this relation
     *
     * @return Lunette_Package_Interface
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Gets the relation type
     *
     * @return Lunette_Package_Relation_Type
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Gets the required version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * Determines if the target package and version is installed
     *
     * @param Lunette_Package_Service $svc
     * @return boolean Checks to see if the target version is installed
     */
    public function isSatisfied( Lunette_Package_Service $svc )
    {
        $version = $svc->getInstalledVersion($this->_name);
        return ( $this->_version ) ? 
            $this->_operator->evaluate($version, $this->_version) :
            ($version !== null); 
    }
    
    /**
     * Parses the requirement information
     *
     * @param string $req
     */
    protected function _parseRequirement( $req )
    {
        $match = array();
        if ( preg_match('/(?P<name>[a-z][a-z0-9\-\.\+]+)(\s*\((?P<op>>>|>=|=|<=|<<)\s*(?P<ver>[^\)]+)\))?/i', $req, $match) ) {
            $this->_name = $match['name'];
            if ( isset($match['op']) ) {
                if ( $match['op'] == '>>' || $match['op'] == '<<' ) {
                    $match['op'] = substr($match['op'], 0, 1);
                }
                $this->_operator = Xyster_Enum::valueOf('Xyster_Data_Operator_Expression', $match['op']);
                $this->_version = $match['ver'];
            }
        }
    }
}