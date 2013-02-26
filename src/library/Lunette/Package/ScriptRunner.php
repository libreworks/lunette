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
 * Package maintainer script runner
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_ScriptRunner
{
    /**
     * @var Lunette_Application
     */
    protected $_app;
    
    /**
     * @var Lunette_Package_Interface
     */
    protected $_pkg;
    
    /**
     * @var string
     */
    protected $_msg = null;
    
    /**
     * Creates a new script runner
     *
     * @param Lunette_Package_Interface $pkg
     */
    public function __construct( Lunette_Application $app, Lunette_Package_Interface $pkg )
    {
        $this->_app = $app;
        $this->_pkg = $pkg;
    }
    
    /**
     * Gets the most recent error message 
     *
     * @return string
     */
    public function getError()
    {
        return $this->_msg;
    }
    
    /**
     * Runs the 'preinst' script
     *
     * @param array $args
     * @return int
     */
    public function preinst( array $args )
    {
        return $this->_run('preinst', $args);
    }
    
    /**
     * Runs the 'postinst' script
     *
     * @param array $args
     * @return int
     */
    public function postinst( array $args )
    {
        return $this->_run('postinst', $args);
    }
    
    /**
     * Runs the 'prerm' script
     *
     * @param array $args
     * @return int
     */
    public function prerm( array $args )
    {
        return $this->_run('prerm', $args);
    }
    
    /**
     * Runs the 'postrm' script
     *
     * @param array $args
     * @return int
     */
    public function postrm( array $args )
    {
        return $this->_run('postrm', $args);
    }
    
    /**
     * Runs the script
     *
     * @param string $scriptName
     * @param array $args
     */
    protected function _run( $scriptName, array $args )
    {
        $result = 0;
        ob_start();
        try {
            if ( $this->_pkg instanceof Lunette_Package_Meta ) {
                $filename = $this->_pkg->getScriptFilename($scriptName);
                if ( $filename !== null ) {
                    $result = $this->_runFile($filename, $args);
                }
                
            } else if ( $this->_pkg instanceof Lunette_Package_Cached ) {
                $source = $this->_pkg->getScript($scriptName);
                if ( strlen(trim($source)) ) {
                    $result = $this->_runSource($source, $args);
                }
            }
        } catch ( Exception $thrown ) {
            $result = 1;
            $this->_msg = $thrown->getMessage();
        }
        $contents = ob_get_clean();
        if ( $result !== 0 && !$this->_msg ) {
            $this->_msg = $contents;
        }
        return $result;
    }
    
    /**
     * Runs a script
     *
     * @param string $filename
     * @param array $args
     * @return int 1 on error, 0 on success
     */
    protected function _runFile( $filename, array $args )
    {
        $return = 0;
        $app = $this->_app;
        $return = @include_once($filename);
        return ( $return === false ) ? 0 : $return;
    }
    
    /**
     * Runs a script from source code
     *
     * @param string $source
     * @param array $args
     * @return int 1 on error, 0 on success
     */
    protected function _runSource( $source, array $args )
    {
        $return = 0;
        $app = $this->_app;
        $return = @eval($source);
        return ( $return === null || $return === false) ? 0 : $return;
    }
}