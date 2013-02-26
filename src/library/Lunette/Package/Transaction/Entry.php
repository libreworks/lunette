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
 * Transaction entry
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Transaction_Entry
{
    /**
     * @var Lunette_Package_Interface
     */
    protected $_pkg;
    protected $_state;
    
    /**
     * Creates a new transaction entry
     *
     * @param Lunette_Package_Interface $pkg
     * @param string $state
     */
    public function __construct( Lunette_Package_Interface $pkg, $state )
    {
        $this->_pkg = $pkg;
        $this->_state = $state;
    }
    
    /**
     * Gets the package 
     *
     * @return Lunette_Package_Interface
     */
    public function getPackage()
    {
        return $this->_pkg;
    }
    
    /**
     * Tests if this entry is an install
     *
     * @return boolean
     */
    public function isInstall()
    {
        return $this->_state == 'i';
    }
    
    /**
     * Tests if this entry is a removal
     *
     * @return boolean
     */
    public function isRemove()
    {
        return $this->_state == 'r' || $this->isPurge();
    }
    
    /**
     * Tests if this entry is a purge
     *
     * @return boolean
     */
    public function isPurge()
    {
        return $this->_state == 'p';
    }
}