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
 * @see Lunette_File_Reader_File
 */
require_once 'Lunette/File/Reader/File.php';
/**
 * Gzip-encoded file reader
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_File
 */
class Lunette_File_Reader_Gz extends Lunette_File_Reader_File
{
    /**
     * Creates a new gzipped file reader
     *
     * @param string $name  The name of the gzipped file to read
     * @throws Lunette_File_Exception if the zlib extension isn't loaded
     */
    public function __construct($filename)
    {
        if ( !extension_loaded('zlib') ) {
            require_once 'Lunette/File/Reader/Exception.php';
            throw new Lunette_File_Reader_Exception('Zlib extension must be enabled to read gzipped files');
        }
        parent::__construct($filename);
    }
    
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
        if ( !gzeof($this->_stream) ) {
            if ( $bytes == -1 ) {
                while( !gzeof($this->_stream) ) {
                    $content .= gzread($this->_stream, 4096);
                }
            } else if ( $bytes > 0 ) {
                $content = gzread($this->_stream, $bytes);
            }
        }
        return $content;
    }
    
    /**
     * Rewinds the file pointer to the beginning
     */
    public function rewind()
    {
        gzrewind($this->_stream);
    }
    
    /**
     * Skips ahead a number of bytes in the stream
     *
     * @param int $bytes 
     */
    public function skip( $bytes )
    {
        $offset = $this->tell();
        gzseek($this->_stream, $this->tell() + $bytes);
    }
    
    /**
     * Tells the current position in bytes in the file
     *
     * @return int
     */
    public function tell()
    {
        return gztell($this->_stream);
    }
    
    /**
     * Does the actual file close
     *
     * @param resource $resource
     */
    protected function _fclose( $resource )
    {
        @gzclose($resource);
    }
        
    /**
     * Does the actual file open 
     *
     * @param string $filename
     * @param string $mode
     * @return resource
     */
    protected function _fopen( $filename, $mode )
    {
        return @gzopen($filename, $mode);
    }
}