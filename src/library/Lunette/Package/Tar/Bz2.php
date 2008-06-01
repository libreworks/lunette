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
 * @see Lunette_Package_Tar
 */
require_once 'Lunette/Package/Tar.php';
/**
 * Lunette Bz2 archive reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Tar_Bz2 extends Lunette_Package_Tar
{
    /**
     * Creates a new Bzip2 tar archive reader
     *
     * @param string $name  The name of the tar archive to read
     * @throws Lunette_Package_Tar_Exception if the bz2 extension isn't loaded
     */
    public function __construct($name)
    {
        if ( !extension_loaded('bz2') ) {
            require_once 'Lunette/Package/Tar/Exception.php';
            throw new Lunette_Package_Tar_Exception('Bz2 extension must be enabled to extract .tar.bz2 archives');
        }
        parent::__construct($name);
    }
    
    /**
     * Closes the file stream
     *
     * @param resource $resource
     */
    protected function _fclose($resource)
    {
        @bzclose($resource);
    }

    /**
     * Gets the file stream
     *
     * @param string $filename
     * @return resource 
     */
    protected function _fopen($filename)
    {
        return @bzopen($filename, "rb");
    }

    /**
     * Reads bytes from the resource
     *
     * @param resource $resource
     * @param int $bytes
     * @return string
     */
    protected function _fread($resource, $bytes)
    {
        return @bzread($resource, $bytes);
    }

    /**
     * Seeks the next block
     *
     * @param resource $resource
     * @param int $length
     * @param int $bytes
     */
    protected function _seekBlock($resource, $length, $bytes)
    {
        for ($i=0; $i<$length; $i++) {
            $this->_readBlock();
        }
    }
}
