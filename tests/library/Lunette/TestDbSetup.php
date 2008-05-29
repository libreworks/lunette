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
                ->execute();
        }
    }
    
    /**
     * Tears down the cache table
     */
    public function tearDownCache()
    {
        $this->_getGateway()->dropTable('lunette_cache');
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