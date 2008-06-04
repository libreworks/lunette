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
 * Tape archive reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Archive_Tar extends Lunette_Package_Archive_Abstract
{
    /**
     * The number of bytes to the end
     *
     * @var int
     */
    protected $_left = 0;
    
    /**
     * The length of the footer
     *
     * @var int
     */
    protected $_footer = 0;
    
    /**
     * Bytes to seek back in order to reach the end of the archive
     *
     * @var int
     */
    protected $_toEnd = null;
    
    /**
     * Creates a new Tar archive reader
     *
     * @param string $filename 
     */
    public function __construct( $filename )
    {
        parent::__construct($this->_getFileReader($filename));
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
            $this->_left = 0;
            $data = $this->_delegate->read($length);
        }
        return $data;
    }
    
    /**
     * Advances to the next file in the archive
     */
    public function next()
    {
        if ($this->_toEnd !== null) {
            return;
        }

        do
        {
            $this->_delegate->skip($this->_left + $this->_footer);
            $rawHeader = $this->_delegate->read(512);
            if (strlen($rawHeader) < 512 || $rawHeader == pack("a512", "")) {
                $this->_toEnd = strlen($rawHeader);
                $this->_currentFilename = false;
                return;
            }

            $header = unpack(
                "a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/".
                "a8checksum/a1type/a100linkname/a6magic/a2version/".
                "a32uname/a32gname/a8devmajor/a8devminor/a155prefix",
                $rawHeader);
                
            $mode = octdec($header['mode']);
            $uid = octdec($header['uid']);
            $gid = octdec($header['gid']);
            $size = octdec($header['size']);
            $mtime = octdec($header['mtime']);
                
            $this->_currentStat = array(
                     2 => $mode,
                'mode' => $mode,
                     4 => $uid,
                 'uid' => $uid,
                     5 => $gid,
                 'gid' => $gid,
                     7 => $size,
                'size' => $size,
                     9 => $mtime,
               'mtime' => $mtime
               );
            
            $this->_currentFilename = (trim($header['magic']) == 'ustar') ?
                $this->_normalizeFilename($header['prefix'] . $header['filename']) :
                $this->_normalizeFilename($header['filename']);

            $this->_left = $this->_currentStat[7];
            $this->_footer = ($this->_left % 512 == 0) ?
                0 : 512 - ($this->_left % 512);

            $checksum = 8 * ord(" ");
            for ($i = 0; $i < 148; $i++) {
                $checksum += ord($rawHeader[$i]);
            }
            for ($i = 156; $i < 512; $i++) {
                $checksum += ord($rawHeader[$i]);
            }

            if (octdec($header['checksum']) != $checksum) {
                require_once 'Lunette/Package/Archive/Exception.php';
                throw new Lunette_Package_Archive_Exception('Invalid checksum for ' .
                    $this->_currentFilename . ' (Got '.$checksum.', expected ' .
                    $header['checksum']);
            }
        } while ($header['type'] != "0");
    }
    
    /**
     * Rewinds the archive to the beginning
     */
    public function rewind()
    {
        $this->_left = 0;
        $this->_footer = 0;
        $this->_toEnd = null;
        $this->_delegate->rewind();
        $this->next();
    }    
}