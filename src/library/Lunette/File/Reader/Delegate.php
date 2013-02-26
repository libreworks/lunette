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
 * @package Lunette_File
 * @version $Id$
 */
/**
 * @see Lunette_File_Reader
 */
require_once 'Lunette/File/Reader.php';
/**
 * Abstract reader with a delegate behind it
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
abstract class Lunette_File_Reader_Delegate extends Lunette_File_Reader
{
    /**
     * @var Lunette_File_Reader
     */
    protected $_delegate;
    
    /**
     * Creates a new delegate reader
     *
     * @param Lunette_File_Reader $delegate
     */
    public function __construct( Lunette_File_Reader $delegate )
    {
        $this->_delegate = $delegate;
    }
    
    /**
     * Gets the delegate reader
     *
     * @return Lunette_File_Delegate
     */
    public function getDelegate()
    {
        return $this->_delegate;
    }
    
    /**
     * Gets the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_delegate->getFilename();
    }

    /**
     * Closes the stream resource
     */
    protected function _close()
    {
        $this->_delegate->_close();
    }
}