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
 * Helper for unpacking a package being installed 
 * 
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Util_Unpacker
{
    /**
     * @var Lunette_Application
     */
    protected $_app;
    
    /**
     * @var Lunette_Package_Service
     */
    protected $_service;
    
    /**
     * @var Lunette_Package_Transaction
     */
    protected $_tx;
    
    /**
     * @var Lunette_Package_Interface
     */
    protected $_pkg;
    
    /**
     * @var Lunette_Package_Runner
     */
    protected $_runner;
    
    /**
     * @var boolean
     */
    protected $_isUpgrade = false;
    
    /**
     * @var Lunette_Package_Cached
     */
    protected $_old;

    /**
     * @var Lunette_Package_Runner
     */
    protected $_oldRunner;
    
    /**
     * @var array
     */
    protected $_errors = array();
    
    protected $_backups = array();
    
    protected $_conflicting = array();
    
    /**
     * Creates a package unpacker
     *
     * @param Lunette_Package_Interface $pkg
     * @param Lunette_Package_Transaction $tx
     */
    public function __construct( Lunette_Application $app, Lunette_Package_Service $service, Lunette_Package_Interface $pkg )
    {
        $this->_service = $service;
        $this->_pkg = $pkg;
        $this->_runner = $pkg->getScriptRunner($app);
        $this->_app = $app;
        
        if ( $oldpkg = $this->_service->getByName($packageName) ) {
            $this->_old = new Lunette_Package_Cached($pkg);
            $this->_isUpgrade = ( $this->_old->getState($service) === Lunette_Package_State::Installed() );
            $this->_oldRunner = $this->_old->getScriptRunner($app);
        }
    }
    
    /**
     * Gets the errors that occurred
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Does the unpack process
     *
     * @param Lunette_Package_Transaction $tx
     */
    public function unpack( Lunette_Package_Transaction $tx )
    {
        $this->_tx = $tx;
        /* @todo do unpack */

        /*
         * Step 1 (PackageInstalled)
         * Step 2 (DeconfigureConflicting)
         * Step 3 (PreInstall)
         * Step 4 (Unpack)
         * Step 5 (PostUpgrade)
         * Step 6 (RemoveOld)
         * Step 7 (FileList)
         * Step 8 (Scripts)
         * Step 9 (Disappear)
         * Step 10 (LobotomizeFilelist)
         * Step 11 (RemoveBackups)
         * Step 12 (UnpackedState)
         * Step 13 (RemoveConflicting) 
         */
    }

    /**
     * Step 1: If a version of the package is already installed
     */
    public function versionInstalled()
    {
        $packageName = $this->_pkg->getControlValue('package');
        $newVersion = $this->_pkg->getControlValue('version');
        if ( $this->_isUpgrade ) {
            if ( $this->_errorsOccurred($oldRunner, 'prerm', array('upgrade', $newVersion)) ) {
                
                if ( $this->_errorsOccurred($this->_runner, 'prerm', array('failed-upgrade', $oldVersion)) ) {
                    
                    if ( $this->_errorsOccurred($this->_oldRunner, 'postinst', array('abort-upgrade', $newVersion)) ) {
                        $this->_service->setState($this->_old, Lunette_Package_State::HalfConfigured());
                    }
                    // else old version is "installed"
                    $this->_exit();
                }
                // still OK to go to step 2
            }
        }
    }
    
    /**
     * Step 2: If a "conflicting" package is being removed at the same time
     */
    public function deconfigureConflicting()
    {
        foreach( $this->_pkg->getRelations(Lunette_Package_Relation_Type::Conflicts()) as $relation ) {
            /* @var $relation Lunette_Package_Relation */
            foreach( $this->_tx as $txEntry ) {
                /* @var $txEntry Lunette_Package_Transaction_Entry */
                if ( $txEntry->isRemove() && $relation->isTarget($txEntry->getPackage()) ) {
                    $this->_conflicting[] = $txEntry;
                }
            }
        }
        
        if ( count($this->_conflicting) ) {
            $pkgName = $this->_pkg->getControlValue('package');
            $newVersion = $this->_pkg->getControlValue('version');
            
            foreach( $this->_conflicting as $confPkg ) {
                /* @var $confPkg Lunette_Package_Interface */
                $crunner = $confPkg->getScriptRunner($this->_app);
                if ( $this->_errorsOccurred($crunner, 'prerm', array('remove', 'in-favour', $pkgName, $newVersion)) ) {
                    
                    if ( $this->_errorsOccurred($crunner, 'postinst', array('abort-remove', 'in-favour', $pkgName, $newVersion)) ) {
                        $this->_service->setState($confPkg, Lunette_Package_State::HalfConfigured());
                    }
                    // else conflicting package is still installed
                }
            }
        }
    }
    
    /**
     * Step 3: Before install
     */
    public function preInstall()
    {
        $packageName = $this->_pkg->getControlValue('package');
        $newVersion = $this->_pkg->getControlValue('version');
        $old = $this->_old;
        $oldVersion = $old ? $old->getControlValue('version') : null;
        $oldState = $old ? $old->getState($this->_service) : null;
        
        if ( $oldState === Lunette_Package_State::Installed() ) {
            
            if ( $this->_errorsOccurred($this->_runner, 'preinst', array('upgrade', $oldVersion)) ) {
                
                if ( $this->_errorsOccurred($this->_runner, 'postrm', array('abort-upgrade', $oldVersion)) ) {
                    
                    $this->_service->setState($old, Lunette_Package_State::HalfInstalled());
                } else {
                    if ( $this->_errorsOccurred($this->_oldRunner, 'postinst', array('abort-upgrade', $newVersion)) ) {
                        $this->_service->setState($old, Lunette_Package_State::UnPacked());
                    }
                    // else old version is installed
                }
                $this->_exit();
            }
            // go to step 4
            
        } else if ( $oldState === Lunette_Package_State::ConfigFiles() ) {
            
            if ( $this->_errorsOccurred($this->_runner, 'preinst', array('install', $oldVersion)) ) {
                
                if ( $this->_errorsOccurred($this->_runner, 'postrm', array('abort-install', $oldVersion)) ) {
                    $this->_service->setState($this->_pkg, Lunette_Package_State::HalfInstalled());
                }
                // else old version is still "ConfigFiles"
                $this->_exit();
            }
            // go to step 4
            
        } else {
            
            if ( $this->_errorsOccurred($this->_runner, 'preinst', array('install')) ) {
                
                if ( $this->_errorsOccurred($this->_runner, 'postrm', array('abort-install')) ) {
                    $this->_service->setState($this->_pkg, Lunette_Package_State::HalfInstalled());
                }
                // else new version is not installed
                $this->_exit();
            }
            // go to step 4
        }        
    }
    
    /**
     * Step 4: Backup old files and unpack new ones
     */
    public function unpack()
    {
        // @todo error if files are being replaced
        
        // @todo need base path 
        
        if ( $this->_old ) {
            // @todo backup old files
            
        }
        
        
        // @todo unpack new files
        
    }

    /**
     * Step 5: Post upgrade
     */
    public function postUpgrade()
    {
        if ( $this->_isUpgrade ) {
            $newVersion = $this->_pkg->getControlValue('version');
            $oldVersion = $this->_old->getControlValue('version');
            if ( $this->_errorsOccurred($this->_oldRunner, 'postrm', array('upgrade', $newVersion)) ) {
                
                if ( $this->_errorsOccurred($this->_runner, 'postrm', array('failed-upgrade', $oldVersion)) ) {
                    
                    if ( $this->_errorsOccurred($this->_oldRunner, 'preinst', array('abort-upgrade', $newVersion)) ) {

                        $this->_service->setState($this->_old, Lunette_Package_State::HalfInstalled());
                        
                    } else {

                        if ( $this->_errorsOccurred($this->_runner, 'postrm', array('abort-upgrade', $oldVersion)) ) {

                            $this->_service->setState($this->_old, Lunette_Package_State::HalfInstalled());
                            
                        } else {
                            
                            if ( $this->_errorsOccurred($this->_oldRunner, 'postinst', array('abort-upgrade', $newVersion)) ) {
                                $this->_service->setState($this->_old, Lunette_Package_State::UnPacked());
                            }   
                        }
                    }
                    // @todo error unwind.  Remove new files, replace old files
                    $this->_exit();
                }
                // go to step 6
            }
            // go to step 6
        }
        // go to step 6
    }
    
    /**
     * Step 6: Remove files in old package that aren't in new
     *
     */
    public function removeOld()
    {
        // @todo remove obsolete files
    }
    
    /**
     * Step 7: Replace old file list
     */
    public function fileList()
    {
        $this->_service->setFiles($this->_pkg, $this->_pkg->getFiles());
    }
    
    /**
     * Step 8: Replace old maintainer scripts
     */
    public function scripts()
    {
        $scripts = array();
        foreach( array('preinst', 'postinst', 'prerm', 'postrm') as $scriptName ) {
            $filename = $this->_pkg->getScriptFilename('preinst');
            if ( $filename !== null ) {
                $scripts[$scriptName] = $filename;
            }
        }
        $this->_service->setScripts($this->_pkg, $scripts);
    }
    
    /**
     * Step 9: Remove disappearing packages
     */
    public function disappear()
    {
        // @todo remove disappearing packages
    }
    
    /**
     * Step 10: Lobotomize conflicting file lists
     */
    public function lobotomizeFilelist()
    {
        // @todo lobotomize conflicting file lists
    }
    
    /**
     * Step 11: Remove backups of old files
     */
    public function removeBackups( )
    {
        // @todo remove old backups
    }
    
    /**
     * Step 12: New package is "Unpacked"
     */
    public function unpackedState()
    {
        $this->_service->setState($this->_pkg, Lunette_Package_State::UnPacked());
    }
    
    /**
     * Step 13: Remove conflicting
     */
    public function removeConflicting()
    {
        foreach( $this->_conflicting as $confPkg ) {
            /* @var $confPkg Lunette_Package_Interface */
            $remover = new Lunette_Package_Remover($this->_app, $this->_service, $confPkg);
            if ( !$remover->removeSkipPre() ) {
                foreach( $remover->getErrors() as $error ) {
                    $this->_errors[] = $error;
                }
            }
        }
    }
    
    /**
     * Runs the script runner and catches any errors
     *
     * @param Lunette_Package_ScriptRunner $runner
     * @param string $method
     * @param array $args
     * @return boolean True if errors, false if no problem
     */
    protected function _errorsOccurred( Lunette_Package_ScriptRunner $runner, $method, array $args )
    {
        $result = $runner->$method($args);
        if ( $result !== 0 ) {
            $this->_errors[] = $runner->getError();
            return true;
        }
        return false;
    }
    
    /**
     * Sets that the installer should exit
     *
     * @param boolean $flag
     */
    protected function _exit( $flag = true )
    {
        $this->_exit = $flag;
    }
}