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
 * @package Lunette_Config
 * @version $Id$
 */
/**
 * Zend_Config
 */
require_once 'Zend/Config.php';
/**
 * @see Lunette_Orm_Service
 */
require_once 'Lunette/Orm/Service.php';
/**
 * Lunette configuration service
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Config
 */
class Lunette_Config_Service extends Lunette_Orm_Service
{
    /**
     * @var string
     */
    protected $_class = 'LunetteConfig';
    
    /**
     * Gets a configuration setting for a extension 
     *
     * @param string $extension The name of the extension
     * @param string $name The configuration setting name
     * @return mixed The setting value
     */
    public function getExtensionConfig( $extension, $name )
    {
        $config = $this->_get($extension, $name);
        return $config !== null ? $config->value : null;
    }
    
    /**
     * Gets all configuration settings for an extension
     *
     * @param string $extension The name of the extension
     * @return Zend_Config
     */
    public function getExtensionConfigAll( $extension )
    {
        return $this->_getMulti($extension);
    }
    
    /**
     * Gets a configuration setting for the Lunette Platform  
     *
     * @param string $name The configuration setting name
     * @return mixed The setting value
     */
    public function getLunetteConfig( $name )
    {
        $config = $this->_get('lunetteplatform', $name);
        return $config !== null ? $config->value : null;
    }
    
    /**
     * Gets all configuration settings for the Lunette Platform
     *
     * @return Zend_Config
     */
    public function getLunetteConfigAll()
    {
        return $this->_getMulti('lunetteplatform');
    }
            
    /**
     * Sets a configuration setting for a extension
     *
     * @param string $extension The name of the extension
     * @param string $name The configuration setting name
     * @param mixed $value The value to set  
     */
    public function setExtensionConfig( $extension, $name, $value )
    {
        $this->_set($extension, $name, $value);
    }
    
    /**
     * Sets more than one configuration setting for an extension
     *
     * The values array must have the configuration setting names as keys.
     * 
     * @param string $extension The name of the extensions
     * @param array $values Associative array of names and values
     */
    public function setExtensionConfigArray( $extension, array $values )
    {
        $this->_setMulti($extension, $values);
    }
    
    /**
     * Sets a configuration setting for the Lunette Platform 
     *
     * @param string $name The configuration setting name
     * @param mixed $value The value to set
     */
    public function setLunetteConfig( $name, $value )
    {
        $this->_set('lunetteplatform', $name, $value);
    }
    
    /**
     * Sets more than one configuration setting for the Lunette Platform
     *
     * The values array must have the configuration setting names as keys.
     * 
     * @param array $values Associative array of names and values
     */
    public function setLunetteConfigArray( array $values )
    {
        $this->_setMulti('lunetteplatform', $values);
    }
    
    /**
     * Gets a configuration setting
     *
     * @param string $system
     * @param string $name
     * @return LunetteConfig or null
     */
    protected function _get( $system, $name )
    {
        return $this->_orm->get('LunetteConfig', array('system'=>$system, 'name'=>$name));
    }
    
    /**
     * Gets all values for a system
     *
     * @param string $system
     * @return Zend_Config
     */
    protected function _getMulti( $system )
    {
        $all = $this->_orm->findAll('LunetteConfig', array('system'=>$system));
        return new Zend_Config($all->fetchPairs('name', 'value'));
    }
    
    /**
     * Sets a configuration setting
     *
     * @param string $system
     * @param string $name
     * @param mixed $value
     */
    protected function _set( $system, $name, $value )
    {
        $config = $this->_get($system, $name);
        if ( $config instanceof LunetteConfig ) {
            $config->value = $value;
        } else {
            $config = new LunetteConfig;
            $config->system = $system;
            $config->name = $name;
            $config->value = $value;
            $this->_orm->persist($config);
        }
        if ( !$this->_delayCommit ) {
            $this->_orm->commit();
        }
    }
    
    /**
     * Sets multiple configuration settings
     *
     * @param string $system
     * @param array $settings
     */
    protected function _setMulti( $system, array $settings )
    {
        $this->_delayCommit = true;
        
        // to cache the ones being set
        $keys = array();
        foreach( array_keys($settings) as $name ) {
            $keys[] = array('system'=>$system, 'name'=>$name);
        }
        $this->_getAll($keys);
        
        foreach( $settings as $name => $value ) {
            $this->_set($system, $name, $value);
        }
        $this->_orm->commit();
        $this->_delayCommit = false;
    }
}