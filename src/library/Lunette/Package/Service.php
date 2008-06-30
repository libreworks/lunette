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
 * @see Lunette_Orm_Service
 */
require_once 'Lunette/Orm/Service.php';
/**
 * Lunette package system service
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Package
 */
class Lunette_Package_Service extends Lunette_Orm_Service 
{
    /**
     * @var string
     */
    protected $_class = 'LunettePackage';
    
    /**
     * Gets a package by name 
     * 
     * @return LunettePackage or null if none
     */
    public function getByName( $name )
    {
        return $this->_orm->find('LunettePackage', array('name' => $name));
    }
    
    /**
     * Gets the version number of the installed package
     *
     * @param string $name
     * @return string or null if package is not installed
     */
    public function getInstalledVersion( $name )
    {
        $pkg = $this->getByName($name);
        return $pkg && $pkg->stateEnum === Lunette_Package_State::Installed() ?
            $pkg->version : null;
    }
    
    /**
     * Gets the packages that have the same files as the package being installed
     * 
     * Packages listed as being replaced by the provided package will not be
     * returned.
     *
     * @param Lunette_Package_Interface $package
     * @return LunettePackageSet
     */
    public function getWithMatchingFiles( Lunette_Package_Interface $package, $ignoreReplaces = true )
    {
        $query = $this->_orm->query('LunettePackage');
        $name = Xyster_Data_Field::named('name');
        $files = Xyster_Data_Field::named('files');
        $query->where($name->neq($package->getControlValue('package')));
        if ( $ignoreReplaces ) {
            foreach( $package->getRelations(Lunette_Package_Relation_Type::Replaces()) as $relation ) {
                /* @var $relation Lunette_Package_Relation */
                $query->where($name->neq($relation->getName()));
            }
        }
        $likes = array();
        foreach( $package->getFiles() as $file ) {
            $likes[] = $files->like("%\n" . $file . "\n%");
        }
        require_once 'Xyster/Data/Junction.php';
        $query->where(Xyster_Data_Junction::fromArray('OR', $likes));
        return $query->execute();
    }
    
    /**
     * Determines if the relations are satisfied for a package 
     *
     * @param Lunette_Package_Interface $package
     * @return boolean
     */
    public function relationsSatisfied( Lunette_Package_Interface $package )
    {
        
    }
        
    /**
     * Sets the filename list for a package 
     *
     * @param Lunette_Package_Interface $package
     * @param array $filenames
     */
    public function setFiles( Lunette_Package_Interface $package, array $filenames )
    {
        $pkg = $this->getByName($package->getControlValue('package'));
        $pkg->files = "\n" . implode("\n", $filenames) . "\n";
        $this->_orm->commit();
    }
    
    /**
     * Replaces the script source for the package given
     * 
     * The scripts array should have the script name as the key (ex. 'postinst')
     * and the filename where the script can be read as the value.  The service
     * will read the contents of the file and insert it into the database
     *
     * @param Lunette_Package_Interface $package
     * @param array $scripts
     * @throws Lunette_File_Reader_Exception if the scripts aren't readable
     */
    public function setScripts( Lunette_Package_Interface $package, array $scripts )
    {
        $pkg = $this->getByName($package->getControlValue('package'));
        $scriptNames = array('preinst', 'postinst', 'prerm', 'postrm');
        require_once 'Lunette/File/Reader/File.php';
        foreach( $scripts as $scriptName => $filename ) {
            if ( in_array($scriptName, $scriptNames) ) {
                $reader = new Lunette_File_Reader_File($filename);
                // $reader will throw exception if file doesn't exist, etc.
                $pkg->$scriptName = $reader->read();
            }
        }
        $this->_orm->commit();
    }
    
    /**
     * Sets the state of the package supplied
     * 
     * If the package record in the system is a version older than the one 
     * provided, the version will be updated.
     *
     * @param Lunette_Package_Interface $package
     * @param Lunette_Package_State $state
     */
    public function setState( Lunette_Package_Interface $package, Lunette_Package_State $state )
    {
        $pkg = $this->getByName($package->getControlValue('package'));
        if ( $pkg->version != $package->getControlValue('version') ) {
            $pkg->version = $package->getControlValue('version');
        }
        $pkg->state = $state->getValue();
        $this->_orm->commit();
    }
}