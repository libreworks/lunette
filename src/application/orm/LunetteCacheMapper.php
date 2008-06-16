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
 * @package Lunette_Orm
 * @version $Id$
 */
/**
 * @see Lunette_Orm_Mapper
 */
require_once 'Lunette/Orm/Mapper.php';
/**
 * Mapper for {@link LunetteCache}
 *
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Orm
 */
class LunetteCacheMapper extends Lunette_Orm_Mapper
{
    protected $_index = array('name_index' => array('name'));
    
    /**
     * Sets up the mapper
     */
    public function init()
    {
        $this->_hasMany('options', array('class'=>'LunetteCacheOption'));
    }
}