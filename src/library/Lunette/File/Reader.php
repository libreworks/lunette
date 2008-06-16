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
 * @package Lunette_Package
 * @version $Id$
 */
/**
 * Abstract reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
abstract class Lunette_Package_Reader
{
    /**
     * @var string
     */
    protected $_filename;
    
    /**
     * @var resource
     */
    protected $_stream;
    
    /**
     * Creates a new reader
     *
     * @param string $filename
     */
    public function __construct( $filename )
    {
        $this->_filename = $this->_normalizeFilename($filename);
        $this->_open();
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->_close();
    }
    
    /**
     * Gets the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Closes the stream resource
     */
    protected function _close()
    {
        if ( $this->_stream !== null ) {
            $this->_fclose($this->_stream);
            $this->_stream = null;
        }
    }
        
    /**
     * Normalizes a filename, stripping out traversals
     * 
     * @param string $filename The filename to normalize
     * @return string
     */
    protected function _normalizeFilename($filename)
    {
        $ok = '';
        if ($filename != '.') {
            $ok = str_replace("\\", "/", $filename);
            while ($ok != ($ok = preg_replace("/[^\/:?]+\/\.\.\//", "", $ok)));
            $ok = str_replace("/./", '', $ok);
            if (substr($ok, 0, 2) == './') {
                $ok = substr($ok, 2);
            }
        }
        return $ok;
    }
    
    /**
     * Opens and returns a stream resource for reading
     *
     * @return resource
     * @throws Lunette_Package_Reader_Exception if an error occurs
     */
    protected function _open()
    {
        if ( $this->_stream === null ) {
            $this->_stream = $this->_fopen($this->_filename, 'rb');
            if ($this->_stream === false) {
                require_once 'Lunette/Package/Reader/Exception.php';
                throw new Lunette_Package_Reader_Exception('Cannot read file: ' . $this->_filename);
            }
        }
        return $this->_stream;
    }
    
    /**
     * Does the actual file close
     *
     * @param resource $resource
     */
    protected function _fclose( $resource )
    {
        @fclose($resource);
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
        return @fopen($filename, $mode);
    }
}