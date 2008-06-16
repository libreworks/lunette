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
 * @see Lunette_File_Reader_Delegate
 */
require_once 'Lunette/File/Reader/Delegate.php';
/**
 * @see Lunette_File_Writer
 */
require_once 'Lunette/File/Writer.php';
/**
 * Zend_Loader
 */
require_once 'Zend/Loader.php';
/**
 * Abstract reader for files with files within them
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
abstract class Lunette_File_Archive_Abstract extends Lunette_File_Reader_Delegate implements Iterator
{
    /**
     * @var string
     */
    protected $_currentFilename = false;
    
    /**
     * @var array
     */
    protected $_currentStat = array();
    
    /**
     * Return the current element
     *
     * @return string
     */
    public function current()
    {
        return $this->_currentFilename;
    }
    
    /**
     * Extracts all files from the archive
     * 
     * The path parameter can either be a {@link Lunette_File_Writer}, a 
     * string path name, or null.  A null path argument will extract files into
     * the current working directory.
     *
     * @param mixed $path The path or a {@link Lunette_File_Writer}
     */
    public function extract( $path = null )
    {
        $this->_extract($path);
    }
    
    /**
     * Returns the contents of the filename specified
     *
     * @param string $filename
     * @return string
     * @throws Lunette_File_Archive_Exception if the file was not found
     * @throws Lunette_File_Exception if there was a problem writing
     */
    public function extractFile( $filename )
    {
        foreach( $this as $currentFilename ) {
            if ( $currentFilename == $filename ) {
                return $this->getFileContents();
            }
        }
        
        require_once 'Lunette/File/Archive/Exception.php';
        throw new Lunette_File_Archive_Exception('File not in archive: ' . $filename);
    }
    
    /**
     * Extracts a list of files from the archive
     * 
     * The list parameter must contain the filenames that should be extracted.
     * Comparison is case sensitive.
     * 
     * The path parameter can either be a {@link Lunette_File_Writer}, a 
     * string path name, or null.  A null path argument will extract files into
     * the current working directory.
     *
     * @param array $list An array of filenames to extract
     * @param mixed $path The path or a {@link Lunette_File_Writer}
     * @throws Lunette_File_Exception if there was a problem writing
     */
    public function extractList( array $list, $path = null )
    {
        $this->_extract($path, $list);
    }
        
    /**
     * Returns the contents of the current file
     *
     * @return string
     */
    abstract public function getFileContents();
    
    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return $this->_currentFilename;
    }
    
    /**
     * Gets all the filenames in the archive
     *
     * This iterates through the file as normal, so it's bad performance to call
     * this method and then work with the archive.
     * 
     * @return array
     */
    public function ls()
    {
        $names = array();
        foreach( $this as $filename ) {
            $names[] = $filename;
        }
        return $names;
    }
    
    /**
     * Gets the stat information for the current file
     *
     * The returned array may consist of:
     * 
     * -  0/dev     device number
     * -  1/ino     inode number
     * -  2/mode    inode protection mode
     * -  3/nlink   number of links
     * -  4/uid     userid of owner
     * -  5/gid     groupid of owner
     * -  6/rdev    device type, if inode device
     * -  7/size    size in bytes
     * -  8/atime   time of last access (Unix timestamp)
     * -  9/mtime   time of last modification (Unix timestamp)
     * - 10/ctime   time of last inode change (Unix timestamp)
     * - 11/blksize blocksize of filesystem IO
     * - 12/blocks  number of blocks allocated
     * 
     * You can retrieve the values by numeric index or string key.  Some entries
     * may not be completed by all archive types 
     *  
     * @return array
     */
    public function stat()
    {
        return $this->_currentStat;
    }
    
    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * @return boolean
     */
    public function valid()
    {
        return ( $this->current() !== false );
    }
    
    /**
     * Extracts some or all files from the archive
     *
     * @param mixed $path Either a writer, a string path, or null
     * @param array $list An optional list of filenames to extract
     */
    protected function _extract( $path, array $list = array() )
    {
        $writer = $this->_getWriter($path);
        $extractAll = count($list) == 0;
        $extracted = 0;
        foreach( $this as $filename ) {
            if ( $extractAll || in_array($filename, $list) ) {
                $writer->write($this->getFileContents(), $filename, $this->stat());
                ++$extracted;
                if ( !$extractAll && $extracted == count($list) ) {
                    return;
                }
            }
        }
    }
    
    /**
     * Gets a reader for the filename specified
     *
     * @param string $filename
     * @return Lunette_File_Reader
     */
    protected function _getFileReader( $filename, $type = 'file' )
    {
        if ( $filename instanceof Lunette_File_Reader ) {
            return $filename;
        } else {
            $className = 'Lunette_File_Reader_' . ucfirst($type);
            Zend_Loader::loadClass($className);
            return new $className($filename);
        }
    }
    
    /**
     * Gets a writer for the supplied path
     *
     * The path parameter can be a Lunette_File_Writer (if so, it will just
     * be returned), a string path name, or null.  A null path parameter will
     * create a writer for the current working directory.
     * 
     * @param mixed $path
     * @return Lunette_File_Writer
     */
    protected function _getWriter( $path = null )
    {
        $writer = null;
        if ( $path instanceof Lunette_File_Writer ) {
            $writer = $path;
        } else {
            $writer = new Lunette_File_Writer(is_string($path) ? $path : null);
        }
        return $writer;
    }
}