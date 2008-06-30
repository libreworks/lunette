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
 * @see Lunette_Package_Util_Unpacker
 */
require_once 'Lunette/Package/Util/Unpacker.php';
/**
 * Package installer
 * 
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Util_Installer
{
    /**
     * @var Lunette_Application
     */
    protected $_app;
    
    /**
     * @var Lunette_Package_Service
     */
    protected $_service;
    
    /**
     * @var array
     */
    protected $_errors = array();
    
    /**
     * Creates a new package installer
     *
     * @param Lunette_Application $app
     * @param Lunette_Package_Service $service
     */
    public function __construct( Lunette_Application $app, Lunette_Package_Service $service )
    {
        $this->_app = $app;
        $this->_service = $service;
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
    
    /**
     * Installs a package
     *
     * @param Lunette_Package_Interface $pkg
     * @param Lunette_Package_Transaction $tx
     * @return boolean
     */
    public function install( Lunette_Package_Interface $pkg, Lunette_Package_Transaction $tx )
    {
        // determine the most-recently configured version
        $oldVersion = null;
        if ( $oldpkg = $this->_service->getByName($packageName) ) {
            $oldPackage = new Lunette_Package_Cached($oldpkg);
            if ( $oldPackage->getState($this->_service) === Lunette_Package_State::Installed() ||
                 $oldPackage->getState($this->_service) === Lunette_Package_State::ConfigFiles() ) {
                $oldVersion = $oldpkg->version;
            }
        }
        
        $unpacker = new Lunette_Package_Util_Unpacker($this->_app, $this->_service, $pkg);
        if ( $unpacker->unpack($tx) ) {
            $runner = $pkg->getScriptRunner($this->_app);
            if ( !$runner->postinst(array('configure', $oldVersion)) ) {
                $this->_errors[] = $runner->getError();
                $this->_service->setState($pkg, Lunette_Package_State::HalfConfigured());
                return false;
            }
            
            // the new package is installed!
            $this->_service->setState($pkg, Lunette_Package_State::Installed());
            return true;
        }
        $this->_errors += $unpacker->getErrors();
        return false;
    }
}