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
 * @see Lunette_Package_Abstract
 */
require_once 'Lunette/Package/Abstract.php';
/**
 * @see Lunette_Package_Archive_Ar
 */
require_once 'Lunette/Package/Archive/Ar.php';
/**
 * @see Lunette_Package_Archive_Tar
 */
require_once 'Lunette/Package/Archive/Tar.php';
/**
 * @see Lunette_Package_Reader_Gz
 */
require_once 'Lunette/Package/Reader/Gz.php';
/**
 * Package information wrapper
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Meta extends Lunette_Package_Abstract
{
    /**
     * @var Lunette_Package_Archive_Ar
     */
    protected $_archive;
        
    /**
     * Creates a new package metadata wrapper
     *
     * The filename parameter can either be the string filename of the archive
     * or a {@link Lunette_Package_Archive_Ar} object.
     * 
     * @param mixed $filename String filename or {@link Lunette_Package_Archive_Ar}
     */
    public function __construct( $filename )
    {
        $this->_archive = $filename instanceof Lunette_Package_Archive_Ar ? 
            $filename : new Lunette_Package_Archive_Ar($filename);
        
        // create the sandbox
        if (!@mkdir($this->_getSandboxName(), 0777)) {
            require_once 'Lunette/Package/Exception.php';
            throw new Lunette_Package_Exception('Could not create the sandbox directory');
        }
        
        // extract and process the control information
        $this->_archive->extractList(array('control.tar.gz'), $this->_getSandboxName());
        $controlFilename = $this->_getSandboxName() . DIRECTORY_SEPARATOR . 'control.tar.gz';
        $control = new Lunette_Package_Archive_Tar(new Lunette_Package_Reader_Gz($controlFilename));
        $this->_process($control->extractFile('control'));
        $control = null;
    }
    
    /**
     * Destroys the object, cleaning up temporary files
     */
    public function __destruct()
    {
        $this->_archive = null;
        $this->_cleanup();
    }
    
    /**
     * Gets the current installed state
     *
     * @param Lunette_Package_Service
     * @return Lunette_Package_State
     */
    public function getState( Lunette_Package_Service $service )
    {
        if ( $this->_state === null ) {
            $package = $service->getByName($this->_control['package']);
            $state = ( $package ) ? $package->state : 0;
            $this->_state = Xyster_Enum::valueOf('Lunette_Package_State', $state);
        }
        return $this->_state;
    }
    
    /**
     * Cleans up the directory
     *
     * @param string $dir
     */
    protected function _cleanup( $dir = null )
    {
        if ( $dir === null ) {
            $dir = $this->_getSandboxName();
        }
        if ( file_exists($dir) ) {
            $directory = new DirectoryIterator($dir);
            foreach( $directory as $v ) {
                if ( !$directory->isDot() ) {
                    if ( $directory->isDir() ) {
                        $this->_cleanup($directory->getRealPath());
                    } else {
                        unlink($directory->getRealPath());
                    }
                }
            }
            rmdir($dir);
        }
    }
    
    /**
     * Gets the sandbox name
     *
     * @return string
     */
    protected function _getSandboxName()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Lunette_Package';
    }
    
    /**
     * Processes the control information
     *
     * @param string $contents
     */
    protected function _process( $contents )
    {
        $lines = explode("\n", $contents);
        $previousField = null;
        foreach( $lines as $line ) {
            $match = array();
            if ( preg_match('/^([a-z\-]+):(.*)$/i', $line, $match) ) {
                $field = strtolower($match[1]);
                $value = trim($match[2]);
                if ( in_array($field, array_keys($this->_control)) && $field != 'format' && $field != 'distribution' ) {
                    $previousField = $field;
                    if ( $field == 'installed-size' ) {
                        $value = (float)$value;    
                    } else if ( $field == 'essential' ) {
                        $value = ($value == 'yes');
                    } else if ( $field == 'priority' ) {
                        $value = in_array($value , Lunette_Package_Abstract::$_priorities) ?
                            $value : 'extra';
                    }
                    $this->_control[$field] = $value;
                }
            } else if ( preg_match('/^\s+/', $line) ) {
                $this->_control[$previousField] .= ' ' . trim($line);
            }
        }
    }
}