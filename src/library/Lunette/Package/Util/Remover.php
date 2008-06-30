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
 * Helper for removing a package 
 * 
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Util_Remover
{
    /**
     * @var Lunette_Package_Service
     */
    protected $_service;
    
    /**
     * @var Lunette_Package_Interface
     */
    protected $_pkg;
    
    /**
     * @var Lunette_Package_Runner
     */
    protected $_runner;
    
    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * Creates a new package remover
     *
     * @param Lunette_Application $app
     * @param Lunette_Package_Service $service
     * @param Lunette_Package_Interface $pkg
     */
    public function __construct( Lunette_Application $app, Lunette_Package_Service $service, Lunette_Package_Interface $pkg )
    {
        $this->_service = $service;
        $this->_pkg = $pkg;
        $this->_runner = $pkg->getScriptRunner($app);
    }
    
    /**
     * Gets the errors that occurred
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
        
    public function remove()
    {
        
    }
    
    public function removeSkipPre()
    {
        
    }
    
    public function purge()
    {
        
    }
}