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
 * @package Lunette_Version
 * @version $Id$
 */
/**
 * Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';
/**
 * Lunette application class
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Application
 */
class Lunette_Application
{
    /**
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Gets the configuration from the Lunette configuration file
     * 
     * Mostly this file contains the database connection settings.  If the file
     * is unavailable, an exception will be thrown.
     *
     * @return Zend_Config
     */
    public function getSystemConfig()
    {
        if ( ! $this->_config instanceof Zend_Config ) {
            $file = $this->_getConfigurationFile();
            if ( !file_exists($file) || !is_readable($file) ) {
                require_once 'Lunette/Config/Exception.php';
                throw new Lunette_Config_Exception('Missing or unreadable configuration file');
            }
            try {
                $this->_config = @new Zend_Config_Ini($file, 'lunette');
                if ( !$this->_config->count() ) {
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception('Configuration file is empty or is malformed');
                }
            } catch ( Zend_Config_Exception $thrown ) {
                require_once 'Lunette/Config/Exception.php';
                throw new Lunette_Config_Exception('Invalid configuration file: ' . $thrown->getMessage());
            }
        }
        return $this->_config;
    }
    
    /**
     * Gets the path to the /application folder
     *
     * @return string
     */
    public function getApplicationPath()
    {
        return dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .
            'application';
    }
    
    /**
     * Gets the path to the configuration file
     *
     * @return string
     */
    protected function _getConfigurationFile()
    {
        return $this->getApplicationPath() . DIRECTORY_SEPARATOR . 'config.ini.php';
    }
}