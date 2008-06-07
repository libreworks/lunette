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
 * Xyster_Enum
 */
require_once 'Xyster/Enum.php';
/**
 * Package installation state
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_State extends Xyster_Enum
{   
    const NotInstalled = 0;
    const UnPacked = 1;
    const HalfConfigured = 2;
    const HalfInstalled = 3;
    const ConfigFiles = 4;
    const Installed = 5;

    /**
     * When a package isn't installed at all
     *
     * @return Lunette_Package_State
     */
    static public function NotInstalled()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * When a package has its files unpacked
     *
     * @return Lunette_Package_State
     */
    static public function UnPacked()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * When a package is half configured
     *
     * @return Lunette_Package_State
     */
    static public function HalfConfigured()
    {
        return Xyster_Enum::_factory();
    }
    
    /**
     * When a package is half installed
     *
     * @return Lunette_Package_State
     */
    static public function HalfInstalled()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * When a package only has database tables installed
     *
     * @return Lunette_Package_State
     */
    static public function ConfigFiles()
    {
       return Xyster_Enum::_factory();
    }
    
    /**
     * When a package is completely installed
     *
     * @return Lunette_Package_State
     */
    static public function Installed()
    {
       return Xyster_Enum::_factory();
    }
}