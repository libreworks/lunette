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
 * @see Lunette_File_Reader
 */
require_once 'Lunette/File/Reader.php';
/**
 * Lunette file reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
class Lunette_File_Reader_File extends Lunette_File_Reader
{
    /**
     * @var array
     */
    protected $_stat;
    
    /**
     * Reads the number of bytes specified
     *
     * If bytes is omitted, the rest of the file is returned
     * 
     * @param int $bytes
     * @return string
     */
    public function read( $bytes = -1 )
    {
        $content = null;
        if ( !feof($this->_stream) ) {
            if ( $bytes == -1 ) {
                $content = stream_get_contents($this->_stream);
            } else if ( $bytes > 0 ) {
                $content = fread($this->_stream, $bytes);
            }
        }
        return $content;
    }
    
    /**
     * Rewinds the file pointer to the beginning
     */
    public function rewind()
    {
        rewind($this->_stream);
    }
    
    /**
     * Skips ahead a number of bytes in the stream
     *
     * @param int $bytes 
     */
    public function skip( $bytes )
    {
        fseek($this->_stream, $bytes, SEEK_CUR);
    }
    
    /**
     * Gets the filesystem statistics for this file
     * 
     * The returned array will consist of:
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
     * You can retrieve the values by numeric index or string key. 
     *
     * @return array
     */
    public function stat()
    {
        if ( $this->_stat === null ) {
            $this->_stat = @fstat($this->_stream);
        }
        return $this->_stat;
    }
    
    /**
     * Tells the current position in bytes in the file
     *
     * @return int
     */
    public function tell()
    {
        return ftell($this->_stream);
    }
}
