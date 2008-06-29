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
 * @see Lunette_Package_Abstract
 */
require_once 'Lunette/Package/Abstract.php';
/**
 * Package information class
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Cached extends Lunette_Package_Abstract
{
    /**
     * @var LunettePackage
     */
    protected $_package;    
    
    /**
     * Creates a new package description object
     *
     * @param LunettePackage $package
     */
    public function __construct( LunettePackage $package )
    {
        $this->_package = $package;
        foreach( $this->_control as $name => $value ) {
            if ( preg_match('/^[a-z]+$/', $name) && $value === null ) {
                $this->_control[$name] = $package->$name;
            }
        }
        $this->_control['package'] = $package->name;
        $this->_control['date'] = $package->packageDate;
        $this->_control['changed-by'] = $package->changedBy;
        $this->_control['installed-size'] = (float)$package->installedSize;
    }

    /**
     * Gets the list of files, excluding directories
     *
     * @return array
     */
    public function getFiles()
    {
        return explode("\n", $this->_package->files);
    }
    
    /**
     * Gets the source for the maintainer script supplied
     *
     * @param string $name
     * @return string
     */
    public function getScript( $name )
    {
        if ( in_array($name, self::$_scripts) ) {
            return $this->_package->$name;
        }
        
        require_once 'Lunette/Package/Exception.php';
        throw new Lunette_Package_Exception('Invalid script name: ' . $name);
    }
    
    /**
     * Gets the current installed state
     *
     * @param Lunette_Package_Service
     * @return Lunette_Package_State
     */
    public function getState( Lunette_Package_Service $service )
    {
        if ( $this->_state === null ) {
            $this->_state = Xyster_Enum::valueOf('Lunette_Package_State',
                $this->_package->state);
        }
        return $this->_state;
    }
}