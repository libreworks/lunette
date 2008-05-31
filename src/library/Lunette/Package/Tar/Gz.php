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
 * Lunette Gz archive reader
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Tar_Gz extends Lunette_Package_Tar
{
    /**
     * Closes the file stream
     *
     * @param resource $resource
     */
    protected function _fclose($resource)
    {
        @gzclose($resource);
    }

    /**
     * Gets the file stream
     *
     * @param string $filename
     * @return resource 
     */
    protected function _fopen($filename)
    {
        return @gzopen($filename, "rb");
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
        return @gzread($resource, $bytes);
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
        @gzseek($resource, gztell($resource) + ($length * $bytes));
    }
}
