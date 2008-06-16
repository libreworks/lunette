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
 * @see Lunette_Package_Archive_Abstract
 */
require_once 'Lunette/Package/Archive/Abstract.php';
/**
 * UNIX Ar reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Archive_Ar extends Lunette_Package_Archive_Abstract
{
    /**
     * The number of bytes to the end
     *
     * @var int
     */
    protected $_left = 0;
    
    /**
     * The size of the header in number of bytes
     * 
     * It's not always 60 bytes since it sometimes contains a long filename
     * 
     * @var int
     */
    protected $_header = 0;

    /**
     * Flag set if there is a 1 byte footer after the data of the current file
     * 
     * @var boolean   
     */
    protected $_footer = false;
    
    /**
     * Creates a new Ar archive reader
     *
     * @param string $filename 
     */
    public function __construct( $filename )
    {
        parent::__construct($this->_getFileReader($filename));
        if ($this->_delegate->read(8) != "!<arch>\n") {
            require_once 'Lunette/Package/Archive/Exception.php';
            throw new Lunette_Package_Archive_Exception('Invalid archive: ' . $this->_delegate->getFilename());
        }
        $this->next();
    }
    
    /**
     * Returns the contents of the current file
     * 
     * This method can only be called once, then the file pointer
     *
     * @return string
     */
    public function getFileContents()
    {
        $data = null;
        $length = $this->_left;
        if ($length > 0) {
            $this->_left -= $length;
            $data = $this->_delegate->read($length);
        }
        return $data;
    }
    
    /**
     * Advances to the next file in the archive
     */
    public function next()
    {
        $this->_delegate->skip($this->_left + ($this->_footer ? 1 : 0));
        
        $name  = trim($this->_delegate->read(16));
        $mtime = trim($this->_delegate->read(12));
        $uid   = (int)trim($this->_delegate->read(6));
        $gid   = (int)trim($this->_delegate->read(6));
        $mode  = octdec(trim($this->_delegate->read(8)));
        $size  = trim($this->_delegate->read(10));
        $delim = $this->_delegate->read(2);
        
        if ($delim === null) {
            $this->_currentFilename = false;
            $this->_currentStat = null;
            $this->_left = 0;
            return;
        }
        
        $this->_footer = ($size % 2 == 1);

        // if the filename starts with a length, then just read the bytes of it
        if (preg_match("/\#1\/(\d+)/", $name, $matches)) {
            $this->_header = 60 + $matches[1];
            $name = $this->_delegate->read($matches[1]);
            $size -= $matches[1];
        } else {
            // strip trailing spaces in name, so we can distinguish spaces in a
            // filename with padding
            $this->_header = 60;
            $name = rtrim($name);
        }

        $this->_left = $size;

        $this->_currentFilename = $this->_normalizeFilename($name);
        $this->_currentStat = array(
            2       => $mode,
            'mode'  => $mode,
            4       => $uid,
            'uid'   => $uid,
            5       => $gid,
            'gid'   => $gid,
            7       => $size,
            'size'  => $size,
            9       => $mtime,
            'mtime' => $mtime
            );
    }
    
    /**
     * Rewinds the archive to the beginning
     */
    public function rewind()
    {
        $this->_left = 0;
        $this->_delegate->rewind();
        $this->_delegate->skip(8);
        $this->next();
    }
}