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
 * @see Lunette_Package_Interface
 */
require_once 'Lunette/Package/Interface.php';
/**
 * @see Lunette_Package_Relation_Set
 */
require_once 'Lunette/Package/Relation/Set.php';
/**
 * @see Lunette_Package_State
 */
require_once 'Lunette/Package/State.php';
/**
 * Abstract package information object
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
abstract class Lunette_Package_Abstract implements Lunette_Package_Interface
{
    /**
     * The package control information
     *
     * @var array
     */
    protected $_control = array(
        'maintainer' => null,
        'uploaders' => null,
        'changed-by' => null,
        'section' => null,
        'priority' => null,
        'package' => '',
        'architecture' => null,
        'essential' => null,
        'depends' => null,
        'recommends' => null,
        'suggests' => null,
        'conflicts' => null,
        'provides' => null,
        'replaces' => null,
        'enhances' => null,
        'version' => null,
        'description' => null,
        'distribution' => 'lunette',
        'date' => 0,
        'format' => 1.5,
        'urgency' => null,
        'installed-size' => null
        );

    /**
     * @var Lunette_Package_State
     */
    protected $_state;
    
    /**
     * @var array
     */
    protected $_relations = array(
        'depends' => null,
        'recommends' => null,
        'suggests' => null,
        'conflicts' => null,
        'provides' => null,
        'replaces' => null,
        'enhances' => null
        );
            
    /**
     * @var array
     */
    protected static $_priorities = array('required', 'important', 'standard',
        'optional', 'extra');
    
    /**
     * Gets the control value
     * 
     * If the name supplied isn't a valid control value, null will be returned.
     *
     * @param string $name The value name
     * @return mixed The control value or null if not found
     */
    public function getControlValue( $name )
    {
        return isset($this->_control[$name]) ? $this->_control[$name] : null;
    }

    /**
     * Gets the packages of a certain relation type
     *
     * @param Lunette_Package_Relation_Type $type The relationship type
     * @return Lunette_Package_Relation_Set
     */
    public function getRelations( Lunette_Package_Relation_Type $type )
    {
        $name = strtolower($type->getName());
        if ( $this->_relations[$name] === null ) {
            $this->_relations[$name] = ( strlen(trim($this->_control[$name])) ) ?
                Lunette_Package_Relation_Set::parse($this, $type, $this->_control[$name])
                : new Lunette_Package_Relation_Set;
            
        }
        return $this->_relations[$name];
    }
    
    /**
     * Gets the string equivalent of the object
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->_control['package'] . ' ' . $this->_control['version'];
    }
}