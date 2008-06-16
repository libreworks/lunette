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
 * @see Lunette_Package_Relation_Interface
 */
require_once 'Lunette/Package/Relation/Interface.php';
/**
 * @see Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
/**
 * @see Lunette_Package_Relation
 */
require_once 'Lunette/Package/Relation.php';
/**
 * Package relation information set
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Relation_Set extends Xyster_Collection_Set implements Lunette_Package_Relation_Interface
{
    /**
     * @var string
     */
    protected $_operator = 'AND';
    
    /**
     * Adds an item to the set
     * 
     * This collection doesn't accept duplicate values, and will return false
     * if the provided value is already in the collection.
     * 
     * It can only accept Lunette_Package_Relation_Interface objects, otherwise
     * it will throw an exception.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     * @throws Lunette_Package_Relation_Exception if the collection cannot contain the value
     */
    public function add( $item )
    {
        if (! $item instanceof Lunette_Package_Relation_Interface ) {
            require_once 'Lunette/Package/Relation/Exception.php';
            throw new Lunette_Package_Relation_Exception('This set can only contain type Lunette_Package_Relation_Interface');
        }
        
        return parent::add($item);
    }
    
    /**
     * Gets the Junction operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->_operator;
    }
        
    /**
     * Determines if the target package and version is installed
     *
     * @param Lunette_Package_Service $svc
     * @return boolean Checks to see if the target version is installed
     */
    public function isSatisfied( Lunette_Package_Service $svc )
    {
        $ok = true;
        if ( $this->_operator == 'OR' ) {
            $ok = false;
            foreach( $this->_items as $pkg ) {
                /* @var $pkg Lunette_Package_Relation_Interface */
                if ( $pkg->isSatisfied($svc) ) {
                    $ok = true;
                    break;
                }
            }
        } else {
            foreach( $this->_items as $pkg ) {
                /* @var $pkg Lunette_Package_Relation_Interface */
                if ( !$pkg->isSatisfied($svc) ) {
                    $ok = false;
                    break;
                }
            }
        }
        return $ok;
    }
    
    /**
     * Creates an 'OR' set
     *
     * @param Lunette_Package_Relation_Interface $left
     * @param Lunette_Package_Relation_Interface $right
     * @return Lunette_Package_Relation_Set
     */
    public static function any( Lunette_Package_Relation_Interface $left, Lunette_Package_Relation_Interface $right )
    {
        $set = new self();
        $set->add($left);
        $set->add($right);
        $set->_operator = 'OR';
        return $set;
    }
    
    /**
     * Parses a dependency string
     * 
     * @param Lunette_Package_Interface $parent The parent package
     * @param Lunette_Package_Relation_Type $type The type of relation
     * @param string $string The depends string
     * @return Lunette_Package_Relation_Set
     */
    public static function parse( Lunette_Package_Interface $parent, Lunette_Package_Relation_Type $type, $string )
    {
        $depends = preg_split('/,\s*/', $string);
        $set = new self;
        foreach( $depends as $depend ) {
            $relation = ( strpos($depend, '|') !== false ) ?
                self::_parseOr($parent, $type, $depend) : 
                new Lunette_Package_Relation($parent, $depend, $type);
            $set->add($relation);
        }
        return $set;
    }
    
    /**
     * Parses a dependency string for OR syntax
     * 
     * @param Lunette_Package_Interface $parent The parent package
     * @param Lunette_Package_Relation_Type $type The type of relation
     * @param string $string The depends string
     * @return Lunette_Package_Relation_Set
     */
    protected static function _parseOr( Lunette_Package_Interface $parent, Lunette_Package_Relation_Type $type, $string )
    {
        $depends = preg_split('/\s*\|\s*/', $string);
        $set = new self();
        $set->_operator = 'OR';
        foreach( $depends as $depend ) {
            $set->add(new Lunette_Package_Relation($parent, $depend, $type));
        }
        return $set;
    }
}