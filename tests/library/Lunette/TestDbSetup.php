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
 * @package Lunette
 * @subpackage Tests
 * @version $Id$
 */
/**
 * Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';
/**
 * Xyster_Db_Gateway_Pdo_Sqlite
 */
require_once 'Xyster/Db/Gateway/Pdo/Sqlite.php';
/**
 * A class to help with the database schema for unit testing
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette
 * @subpackage Tests
 */
class Lunette_TestDbSetup
{
    /**
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected static $_db;
    
    /**
     * @var Xyster_Db_Gateway_Pdo_Sqlite
     */
    protected static $_gateway;
    
    /**
     * Sets up the config database table
     */
    public function setupConfig()
    {
        $db = $this->getDbAdapter();
        if ( in_array('lunette_config', $db->listTables()) ) {
            $db->query('DELETE FROM lunette_config');
        } else {
            $this->_getGateway()->createTable('lunette_config')
                ->addVarchar('system', 255)
                ->addVarchar('name', 255)
                ->addVarchar('value', 255)
                ->primaryMulti(array('system', 'name'))
                ->execute();
        }
    }
    
    /**
     * Tears down the config table
     */
    public function tearDownConfig()
    {
        $this->_getGateway()->dropTable('lunette_config');
    }
    
    /**
     * Sets up the cache table
     */
    public function setupCache()
    {
        $db = $this->getDbAdapter();
        if ( in_array('lunette_cache', $db->listTables()) ) {
            $db->query('DELETE FROM lunette_cache');
        } else {
            $this->_getGateway()->createTable('lunette_cache')
                ->addIdentity('lunette_cache_id')
                ->addVarchar('name', 255)->unique()
                ->addVarchar('type', 255)
                ->addVarchar('cache_id_prefix', 255)->null()
                ->addInteger('lifetime')->defaultValue(3600)->null()
                ->addBoolean('write_control')->defaultValue(true)->null()
                ->addBoolean('automatic_serialization')->defaultValue(false)->null()
                ->addInteger('automatic_cleaning_factor')->defaultValue(10)->null()
                ->addBoolean('ignore_user_abort')->defaultValue(false)->null()
                ->execute();
        }
        if ( in_array('lunette_cache_option', $db->listTables()) ) {
            $db->query('DELETE FROM lunette_cache_option');
        } else {
            $this->_getGateway()->createTable('lunette_cache_option')
                ->addIdentity('lunette_cache_option_id')
                ->addInteger('lunette_cache_id')
                ->addVarchar('name', 255)
                ->addVarchar('value', 255)
                ->execute();
        }
    }
    
    /**
     * Tears down the cache table
     */
    public function tearDownCache()
    {
        $this->_getGateway()->dropTable('lunette_cache');
        $this->_getGateway()->dropTable('lunette_cache_option');
    }
    
    /**
     * Sets up the package table
     */
    public function setupPackage()
    {
        $db = $this->getDbAdapter();
        if ( in_array('lunette_package', $db->listTables()) ) {
            $db->query('DELETE FROM lunette_package');
        } else {
            $this->_getGateway()->createTable('lunette_package')
                ->addIdentity('lunette_package_id')
                ->addVarchar('name', 50)->unique() // man
                ->addVarchar('maintainer', 255) // man
                ->addVarchar('version', 25) // man
                ->addClob('description') // man
                ->addInteger('state')->defaultValue(0)
                ->addVarchar('uploaders', 255)->null()
                ->addVarchar('changed_by', 255)->null()
                ->addVarchar('section', 50)->null()
                ->addVarchar('priority', 25)->null()
                ->addVarchar('architecture', 25)->null()
                ->addVarchar('essential', 10)->null()
                ->addClob('depends')->null()
                ->addClob('recommends')->null()
                ->addClob('suggests')->null()
                ->addClob('conflicts')->null()
                ->addClob('provides')->null()
                ->addClob('replaces')->null()
                ->addClob('enhances')->null()
                ->addTimestamp('package_date')->null()
                ->addVarchar('urgency', 15)->null()
                ->addFloat('installed_size')->null()
                ->execute();
        }
    }
    
    /**
     * Tears down the package table
     */
    public function tearDownPackage()
    {
        $this->_getGateway()->dropTable('lunette_package');
    }
    
    /**
     * Gets the database adapter
     *
     * @return Zend_Db_Adapter_Pdo_Sqlite
     */
    public function getDbAdapter()
    {
        if ( !self::$_db ) {
            self::$_db = new Zend_Db_Adapter_Pdo_Sqlite(array('dbname'=>':memory:'));
        }
        return self::$_db;
    }
    
    /**
     * Gets the database gateway
     * 
     * @return Xyster_Db_Gateway_Pdo_Sqlite
     */
    protected function _getGateway()
    {
        if ( !self::$_gateway ) {
            self::$_gateway = new Xyster_Db_Gateway_Pdo_Sqlite($this->getDbAdapter()); 
        }
        return self::$_gateway;
    }
}