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
 * @package Lunette_Cache
 * @version $Id$
 */
/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
/**
 * @see Lunette_Orm_Service
 */
require_once 'Lunette/Orm/Service.php';
/**
 * Lunette cache system service
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Cache
 */
class Lunette_Cache_Service extends Lunette_Orm_Service
{
    /**
     * @var string
     */
    protected $_class = 'LunetteCache';
    
    /**
     * Creates a new cache system
     *
     * @param string $name The name of the cache
     * @param string $type The class name used by the cache system
     * @param array $values The configuration options. Depends on class.
     * @return LunetteCache
     */
    public function create( $name, $type, array $values = array() )
    {
        if ( $this->isCacheSystem($name) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('A cache system with this name already exists');
        }
        $cache = new LunetteCache;
        $cache->name = $name;
        $cache->type = $type;
        $this->_orm->persist($cache);
        if ( !$this->_delayCommit ) {
            $this->_orm->commit();
        }
        return $cache;
    }
    
    /**
     * Deletes a cache system
     *
     * @param LunetteCache $cache The system to remove
     */
    public function delete( LunetteCache $cache )
    {
        $this->_orm->remove($cache);
        if ( !$this->_delayCommit ) {
            $this->_orm->commit();
        }
    }

    /**
     * Gets the cache system by name 
     * 
     * @return LunetteCache or null if none
     */
    public function getByName( $name )
    {
        return $this->_orm->find('LunetteCache', array('name' => $name));
    }
    
    /**
     * Gets the correct Zend_Cache object for a cache system name
     *
     * @param string $name
     * @return Zend_Cache_Core
     */
    public function getSystemByName( $name )
    {
        $cache = $this->getByName($name);
        return Zend_Cache::factory('core',
            str_replace('Zend_Cache_Backend_', '', $cache->type), array(), array());
    }
    
    /**
     * Tests whether the name given is a defined cache system
     * 
     * @param string $name The name of the cache system
     * @return boolean
     */
    public function isCacheSystem( $name )
    {
        return $this->getByName($name) instanceof LunetteCache;
    }
}