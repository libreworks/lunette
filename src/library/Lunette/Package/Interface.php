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
 * Package information class
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
interface Lunette_Package_Interface
{
    /**
     * Gets the control value
     * 
     * If the name supplied isn't a valid control value, null will be returned.
     *
     * @param string $name The value name
     * @return mixed The control value or null if not found
     */
    function getControlValue( $name );

    /**
     * Gets the list of files, excluding directories
     *
     * @return array
     */
    function getFiles();
    
    /**
     * Gets the packages of a certain relation type
     *
     * @param Lunette_Package_Relation_Type $type The relationship type
     * @return Lunette_Package_Relation_Set
     */
    function getRelations( Lunette_Package_Relation_Type $type );
    
    /**
     * Gets a script runner for this package
     *
     * @param Lunette_Application $app
     * @return Lunette_Package_ScriptRunner
     */
    function getScriptRunner( Lunette_Application $app );
    
    /**
     * Gets the current installed state
     *
     * @param Lunette_Package_Service $service
     * @return Lunette_Package_State
     */
    function getState( Lunette_Package_Service $service );
    
    /**
     * Gets the string equivalent of the object
     *
     * The string value is expected to be the package name and version
     * 
     * @return string
     */
    function __toString();
}