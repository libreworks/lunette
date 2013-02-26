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
 * @package Lunette_Orm
 * @version $Id$
 */
/**
 * Xyster_Orm_Entity
 */
require_once 'Xyster/Orm/Entity.php';
/**
 * A cache system
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Orm
 */
class LunetteCache extends Xyster_Orm_Entity
{
    /**
     * Returns the frontend options
     *
     * @return array
     */
    public function getFrontendOptions()
    {
        return array(
                'cache_id_prefix' => $this->cacheIdPrefix,
                'lifetime' => intval($this->lifetime),
                'write_control' => $this->writeControl,
                'automatic_serialization' => $this->automaticSerialization,
                'automatic_cleaning_factor' => intval($this->automaticCleaningFactor),
                'ignore_user_abort' => $this->ignoreUserAbort
            );
    }
    
    /**
     * Gets the values of the options assigned to this cache system
     *
     * @return array
     */
    public function getBackendOptions()
    {
        $options = array();
        foreach( $this->options as $option ) {
            /* @var $option LunetteCacheOption */
            $options[$option->name] = $option->getRealValue();
        }
        return $options;
    }
}