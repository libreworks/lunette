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
 * Xyster_Collection_Set
 */
require_once 'Xyster/Collection/Set.php';
/**
 * @see Lunette_Package_Transaction_Entry
 */
require_once 'Lunette/Package/Transaction/Entry.php';
/**
 * Transaction entry
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Transaction extends Xyster_Collection_Set
{
    const INSTALL = 'i';
    const REMOVE = 'r';
    const PURGE = 'p';

    /**
     * Adds an item to the set
     * 
     * This collection doesn't accept duplicate values, and will return false
     * if the provided value is already in the collection.
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     */
    public function add( $item )
    {
        if ( $item instanceof Lunette_Package_Transaction_Entry ) {
            return parent::add($item);
        }
        require_once 'Lunette/Package/Transaction/Exception.php';
        throw new Lunette_Package_Transaction_Exception('This set only allows Lunette_Package_Transaction_Entry objects');
    }
    
    /**
     * Adds an installation to the transaction
     *
     * @param Lunette_Package_Interface $pkg
     */
    public function installPackage( Lunette_Package_Interface $pkg )
    {
        $this->add(new Lunette_Package_Transaction_Entry($pkg, self::INSTALL));
    }
    
    /**
     * Adds a removal or purge to the transaction
     *
     * @param Lunette_Package_Interface $pkg
     * @param boolean $purge 
     */
    public function removePackage( Lunette_Package_Interface $pkg, $purge = false )
    {
        $this->add(new Lunette_Package_Transaction_Entry($pkg,
            $purge ? self::PURGE : self::REMOVE ));
    }
}