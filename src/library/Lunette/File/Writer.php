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
 * File and directory writer
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
class Lunette_File_Writer
{
    /**
     * @var string
     */
    protected $_base;
    
    /**
     * Creates a new writer
     *
     * @param string $base The directory to extract archive contents
     */
    public function __construct($base = null)
    {
        if ( $base === null || $base === '' ) {
            $base = getcwd();
        }
        $this->_base = (substr($base, -1) == '/') ? $base : $base . '/'; 
    }
    
    /**
     * Gets the base directory where files will be written
     *
     * @return string
     */
    public function getBase()
    {
        return $this->_base;
    }
    
    /**
     * Gets the filename prepended with the base
     *
     * @param string $filename
     * @return string
     */
    public function getFilename($filename)
    {
        return $this->_base . $filename;
    }
    
    /**
     * Gets a writable stream resource for a filename
     *
     * @param string $filename
     * @return resource
     */
    public function getWriteStream( $filename )
    {
        if ( is_dir($filename) ) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('File already exists as a directory: ' . $filename);
        }
        $file = @fopen($filename, 'wb');
        if ($file === false) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('Cannot write to file: ' . $filename);
        }
        return $file;
    }
    
    /**
     * Creates a directory in relation to the writer base
     *
     * @param string $directory The relative directory name
     * @throws Lunette_File_Exception if there was a problem creating it
     */
    public function mkdir( $name )
    {
        $name = str_replace('\\', '/', $name);
        while ( $name[0] == '/' ) {
            $name = substr($name, 1);
        }
        $this->_mkdir($this->getFilename($name));
    }
    
    /**
     * Writes the supplied data to the filename
     *
     * The stat array should contain 'mtime', 'mode', and 'size'
     * 
     * @param string $data
     * @param string $filename
     * @param array $stat
     * @throws Lunette_File_Exception if there was a problem writing
     */
    public function write($data, $filename, array $stat = array())
    {
        // put file where we expect it
        $proper = $this->getFilename($filename);
        // create the directory where the file should go 
        $this->_mkdir(dirname($proper));
        
        $stream = $this->getWriteStream($proper);
        if (!@fwrite($stream, $data)) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('Could not write to: ' . $proper);
        }
        @fclose($stream);
        
        if (isset($stat['mtime'])) {
            @touch($proper, $stat['mtime']);
        }
        if (isset($stat['mode'])) {
            @chmod($proper, $stat['mode']);
        }
        if (isset($stat['size']) && filesize($proper) != $stat['size']) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('Extracted file ' . $proper .
                ' has ' . filesize($proper) . ' bytes but ' . $stat['size'] .
                ' was expected)');
        }
    }
    
    /**
     * Creates a directory
     *
     * @param string $directory The directory name
     * @throws Lunette_File_Exception if there was a problem creating it
     */
    protected function _mkdir($directory)
    {
        if (@is_dir($directory) || $directory == '') {
            return;
        }
        if ( substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }
        if (@file_exists($directory) && !@is_dir($directory)) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception('Directory already exists as a file: ' . $directory);
        }

        $parentDir = dirname($directory);
        if ($parentDir != $directory && $parentDir != '' ) {
             $this->_mkdir($parentDir); // exception if error
        }

        if (!@mkdir($directory, 0777)) {
            require_once 'Lunette/File/Exception.php';
            throw new Lunette_File_Exception("Unable to create directory: " . $directory);
        }        
    }    
}