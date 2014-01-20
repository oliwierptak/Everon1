<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Domain\Interfaces\Entity;
use Everon\DataMapper\Interfaces\Schema\Table;

interface DataMapper
{
    function add(Entity $Entity);
    function save(Entity $Entity);
    function delete(Entity $Entity);

    /**
     * @param int $id
     * @return Entity
     */
    function fetchOne($id);
    
    function fetchAll(array $criteria);

    /**
     * @return Table
     */    
    function getTable();
}
