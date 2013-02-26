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
 * Base service for services that interact with the ORM layer
 *
 * @copyright Copyright (c) LibreWorks (http://libreworks.com)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette_Orm
 */
class Lunette_Orm_Service
{
    /**
     * @var boolean
     */
    protected $_delayCommit = false;
    
    /**
     * @var Xyster_Orm
     */
    protected $_orm;

    /**
     * The class name of the ORM entity to use  
     * @var string
     */
    protected $_class;
    
    /**
     * Creates the cache service
     *
     * @param Xyster_Orm $orm The orm session
     */
    public function __construct( Xyster_Orm $orm )
    {
        $this->_orm = $orm;
    }

    /**
     * Gets the entities in the data store
     *
     * @param array $ids Optional. The primary keys of entities to get.
     * @return Xyster_Orm_Set
     */
    protected function _getAll( array $ids = null )
    {
        return $this->_orm->getAll($this->_class, $ids);
    }
}