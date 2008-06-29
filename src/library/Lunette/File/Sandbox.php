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
 * @package Lunette_File
 * @version $Id$
 */
/**
 * A file sandbox, files deleted after garbage collected
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
class Lunette_File_Sandbox implements IteratorAggregate
{
    /**
     * The name of the sandbox
     *
     * @var string
     */
    protected $_namespace = 'LunetteSandbox';
    
    /**
     * The filename of the sandbox
     *
     * @var string
     */
    protected $_realpath;
    
    /**
     * Creates a new sandbox
     *
     * @param string $namespace
     */
    public function __construct( $namespace = null )
    {
        if ( $namespace ) {
            $this->_namespace = $namespace;
        }
        $this->_realpath = sys_get_temp_dir() . DIRECTORY_SEPARATOR .
            $this->_namespace;

        // create the sandbox
        if (!@mkdir($this->_realpath, 0777)) {
            $error = error_get_last();
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('Could not create the sandbox directory: ' . $error['message']);
        }
    }
    
    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->_cleanup();
    }
    
    /**
     * Gets an iterator for the contents of the sandbox
     *
     * @return DirectoryIterator
     */
    public function getIterator()
    {
        return new DirectoryIterator($this->_realpath);
    }
    
    /**
     * Gets the namespace for the sandbox
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }
    
    /**
     * Gets the real path filename of the sandbox
     *
     * @return string
     */
    public function getRealpath()
    {
        return $this->_realpath;
    }
    
    /**
     * Cleans up the directory.
     *
     * @param string $dir
     */
    protected function _cleanup( $dir = null )
    {
        if ( $dir === null ) {
            $dir = $this->_realpath;
        }
        if ( @file_exists($dir) ) {
            $directory = new DirectoryIterator($dir);
            foreach( $directory as $v ) {
                if ( !$directory->isDot() ) {
                    if ( $directory->isDir() ) {
                        $this->_cleanup($directory->getRealPath());
                    } else {
                        @unlink($directory->getRealPath());
                    }
                }
            }
            @rmdir($dir);
        }
    }
}