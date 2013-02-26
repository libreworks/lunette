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
 * @package Lunette_Package
 * @version $Id$
 */
/**
 * @see Lunette_File_Writer
 */
require_once 'Lunette/File/Writer.php';
/**
 * Package file extractor
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Util_Extractor extends Lunette_File_Writer
{
    /**
     * The old files backed up
     *
     * @var array
     */
    protected $_backs = array();
    
    /**
     * Backs up all of the old files
     */
    public function backupOld( Lunette_Package_Interface $package )
    {
        $copyInsteadOfLink = !strcasecmp(substr(PHP_OS, 0, 3), 'WIN');
        foreach( $package->getFiles() as $filename ) {
            $tfilename = $this->_base . $filename;
            if ( @file_exists($tfilename) ) {
                if ( $copyInsteadOfLink ?
                    @copy($tfilename, $tfilename . '.pkg.tmp') : 
                    @link($tfilename, $tfilename . '.pkg.tmp') ) {
                    $this->_backs[] = $tfilename . '.pkg.tmp';
                }
            }
        }
    }
    
    /**
     * Gets the filename prepended with the base
     *
     * @param string $filename
     * @return string
     */
    public function getFilename($filename)
    {
        return parent::getFilename($filename) . '.pkg.new';
    }
    
    /**
     * Puts the old files back in place, used as a roll-back
     */
    public function replaceOld()
    {
        $deleteFirst = !strcasecmp(substr(PHP_OS, 0, 3), 'WIN');
        foreach( $this->_backs as $filename ) {
            $realFilename = substr($filename, 0, -8);
            if ( $deleteFirst && @file_exists($realFilename) ) {
                @unlink($realFilename);
            }
            @rename($filename, $realFilename);
        }
    }
    
    /**
     * Removes all of the backed-up old files
     */
    public function removeOld()
    {
        foreach( $this->_backs as $filename ) {
            @unlink($filename);
        }
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
        parent::write($data, $filename, $stat);
        
        // without the .pkg.new
        $realFilename = substr($this->getFilename($filename), 0, -8);
        
        // apparently, Windows won't let you mv over an existing file
        if ( !strcasecmp(substr(PHP_OS, 0, 3), 'WIN') && @file_exists($realFilename) ) {
            @unlink($realFilename);
        }
        @rename($this->getFilename($filename), $realFilename);
    }
}