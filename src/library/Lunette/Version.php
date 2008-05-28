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
 * @package Lunette_Version
 * @version $Id$
 */
/**
 * Lunette information class
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Version
 */
final class Lunette_Version
{
    /**
     * Lunette platform version
     * 
     * @see compareVersion
     */
    const VERSION = '0.1.0';
    
    /**
     * Compare the specified version string with the current version
     *
     * @param string $version  A version string (e.g. "1.2.3").
     * @return int -1 if the $version is older, 0 if they are the same, and +1 if $version is newer.
     */
    public static function compareVersion($version)
    {
        return version_compare($version, self::VERSION);
    }
}