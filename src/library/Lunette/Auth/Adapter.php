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
 * Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';
/**
 * Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';
/**
 * Default Lunette authentication adapter
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Auth
 */
class Lunette_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     * @var Lunette_User_Service
     */
    protected $_service;
    
    /**
     * The email address of the account being authenticated
     *
     * @var string
     */
    protected $_email = null;

    /**
     * The password of the account being authenticated
     *
     * @var string
     */
    protected $_password = null;
    
    /**
     * Creates a new Lunette_Auth_Adapter
     *
     * @param Lunette_User_Service $service
     * @param string $email
     * @param string $password
     */
    public function __construct( Lunette_User_Service $service, $email = null, $password = null )
    {
        $this->_service = $service;
        if ($email !== null) {
            $this->setEmail($email);
        }
        if ($password !== null) {
            $this->setPassword($password);
        }
    }
    
    /**
     * Returns the email address of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets the username
     *
     * @param  string $email The email address
     * @return Lunette_Auth_Adapter Provides a fluent interface
     */
    public function setEmail($email)
    {
        $this->_email = (string) $email;
        return $this;
    }

    /**
     * Returns the password of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the password for the account
     *
     * @param  string $password The password of the account being authenticated
     * @return Lunette_Auth_Adapter Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }
    
    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $result = $this->_service->authenticate($this->_email, $this->_password);
        $identity = $result > 0 ? $this->_email : null;
        return new Zend_Auth_Result($result, $identity);
    }
}