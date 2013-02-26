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
 * @see Lunette_Orm_Mapper
 */
require_once 'Lunette/Orm/Mapper.php';
/**
 * Xyster_Orm_Entity_Lookup_Enum
 */
require_once 'Xyster/Orm/Entity/Lookup/Enum.php';
/**
 * Xyster_Type
 */
require_once 'Xyster/Type.php';
/**
 * @see Lunette_Package_State
 */
require_once 'Lunette/Package/State.php';
/**
 * A package mapper
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Orm
 */
class LunettePackageMapper extends Lunette_Orm_Mapper
{
    protected $_index = array('name_index' => array('name'));
    
    public function init()
    {
        $type = $this->getEntityType();
        $lookup = new Xyster_Orm_Entity_Lookup_Enum($type,
            new Xyster_Type('Lunette_Package_State'), 'state');
        $type->addLookup($lookup);
    }
}