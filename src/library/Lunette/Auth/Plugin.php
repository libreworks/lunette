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
 * @package Lunette_Auth
 * @version $Id$
 */
/**
 * @see Lunette_Auth_Plugin_Interface
 */
require_once 'Lunette/Auth/Plugin/Interface.php';
/**
 * Default Lunette authentication plugin
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Auth
 */
class Lunette_Auth_Plugin implements Lunette_Auth_Plugin_Interface
{
    /**
     * @var Lunette_User_Service
     */
    protected $_service;
    
    /**
     * Creates a new authentication plugin
     *
     * @param Lunette_User_Service $service
     */
    public function __construct( Lunette_User_Service $service )
    {
        $this->_service = $service;
    }
    
    /**
     * Gets whether the password associated with the user can be changed 
     *
     * @return boolean
     */
    public function canChangePassword()
    {
        return true;
    }
    
    /**
     * Gets the authentication adapter
     *
     * @param array $creds Optional array of credentials
     * @return Zend_Auth_Adapter_Interface
     */
    public function getAuthAdapter( array $creds = array() )
    {
        $user = isset($creds['email']) ? $creds['email'] : null;
        $password = isset($creds['password']) ? $creds['password'] : null;
        return new Lunette_Auth_Adapter($this->_service, $user, $password);
    }
    
    /**
     * Gets the form to display for user input
     *
     * @return Zend_Form or null if no form is needed
     */
    public function getForm()
    {
        require_once 'Lunette/Auth/Form.php';
        return new Lunette_Auth_Form;
    }
    
    /**
     * Return user settings for new users if they're automatically available
     * 
     * @param string $username
     * @return array
     */
    public function getUserInfo( $username )
    {
        return array();
    }
    
    /**
     * Sets the password for a user
     *
     * @param string $username The username of the account
     * @param string $password The new password
     */
    public function setPassword( $username, $password )
    {
        $user = $this->_service->getByUsername($username);
        if ( $user instanceof LunetteUser ) {
            $this->_service->setPassword($user, $password);
        }
    }
    
    /**
     * Whether this plugin supports auto-authentication
     *
     * @return boolean
     */
    public function supportsAutoAuth()
    {
        return false;
    }
}