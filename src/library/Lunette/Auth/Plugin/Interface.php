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
 * @package Lunette_Auth
 * @version $Id$
 */
/**
 * Lunette authentication plugin
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Auth
 */
interface Lunette_Auth_Plugin_Interface
{
    /**
     * Gets whether the password associated with the user can be changed 
     *
     * @return boolean
     */
    function canChangePassword();
    
    /**
     * Gets the authentication adapter
     * 
     * If you're clever with interface usage, you can make your auth plugin the
     * adapter as well.
     *
     * @param array $creds Optional array of credentials
     * @return Zend_Auth_Adapter_Interface
     */
    function getAuthAdapter( array $creds = array() );
    
    /**
     * Gets the form to display for user input
     *
     * @return Zend_Form or null if no form is needed
     */
    function getForm();
    
    /**
     * Return user settings for new users if they're automatically available
     *
     * The user info array needs to be an associative array of values available
     * as fields on the {@link LunetteUser} class.
     * 
     * @param string $username
     * @return array
     */
    function getUserInfo( $username );
    
    /**
     * Sets the password for a user
     * 
     * Lunette will only call this method if {@link canChangePassword} returns
     * true.  Feel free to handle errors appropriately anyway.
     *
     * @param string $username The username of the account
     * @param string $password The new password
     */
    function setPassword( $username, $password );
    
    /**
     * Whether this plugin supports auto-authentication
     * 
     * Auto-authentication requires no input from the user.  The user's identity
     * is predetermined, for example, the DN in an SSL client certificate or
     * another HTTP header
     *
     * @return boolean
     */
    function supportsAutoAuth();
}