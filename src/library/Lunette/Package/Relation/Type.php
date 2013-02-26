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
 * @package Lunette_Package
 * @version $Id$
 */
/**
 * Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Package relation type 
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Relation_Type extends Xyster_Enum
{
    const Depends = 0;
    const Suggests = 1;
    const Recommends = 2;
    const Conflicts = 3;
    const Replaces = 4;
    const Enhances = 5;
    const Provides = 6;
    
    /**
     * For when a package depends on another package
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Depends()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When another package could prove useful (opposite of enhances)
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Suggests()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When a package is strongly recommended
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Recommends()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When a package conflicts with another one
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Conflicts()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When a package replaces another one
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Replaces()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When a package can enhance features of another package
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Enhances()
    {
        return Xyster_Enum::_factory(); 
    }
    
    /**
     * When a package provides a virtual package
     *
     * @return Lunette_Package_Relation_Type
     */
    static public function Provides()
    {
        return Xyster_Enum::_factory(); 
    }
}