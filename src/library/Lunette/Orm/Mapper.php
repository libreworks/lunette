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
 * Xyster_Orm_Mapper
 */
require_once 'Xyster/Orm/Mapper.php';
/**
 * Lunette base object-relational mapper class
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Orm
 */
class Lunette_Orm_Mapper extends Xyster_Orm_Mapper
{
    protected $_domain = 'lunette';
}