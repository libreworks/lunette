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
 * @package Lunette_User
 * @version $Id$
 */
/**
 * @see Lunette_Orm_Service
 */
require_once 'Lunette/Orm/Service.php';
/**
 * User service
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_User
 */
class Lunette_User_Service extends Lunette_Orm_Service
{
    protected $_class = 'LunetteUser';
    
    /**
     * Returns a Zend_Auth_Result constant for the email and password given
     *
     * @param unknown_type $email
     * @param unknown_type $password
     */
    public function authenticate( $email, $password )
    {
        require_once 'Zend/Auth/Result.php';
        if ( $user = $this->getByEmail($email) ) {
            return ( $user->password == md5($password) ) ?
                Zend_Auth_Result::SUCCESS :
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
        } else {
            return Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
        }
    }
    
    /**
     * Gets the user account by e-mail or null if not found
     *
     * @param string $email
     * @return LunetteUser
     */
    public function getByEmail( $email )
    {
        return $this->_orm->find($this->_class, array('email'=>$email));
    }
    
    /**
     * Gets the user account by username or null if not found
     *
     * @param string $username
     * @return LunetteUser
     */
    public function getByUsername( $username )
    {
        return $this->_orm->find($this->_class, array('username'=>$username));
    }
    
    /**
     * Sets the user's password
     *
     * @param LunetteUser $user
     * @param string $password
     */
    public function setPassword( LunetteUser $user, $password )
    {
        $user->password = md5($password);
        if ( !$this->_delayCommit ) {
            $this->_orm->commit();
        }
    }
}
