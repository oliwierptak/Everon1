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
use Everon\DataMapper\Interfaces\Criteria;
use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Interfaces\Schema\Table;

interface DataMapper
{
    /**
     * @param Entity $Entity
     * @return int Return last inserted ID
     */
    function add(Entity $Entity);

    /**
     * @param Entity $Entity
     * @return bool
     */
    function save(Entity $Entity);

    /**
     * @param Entity $Entity
     * @return bool
     */
    function delete(Entity $Entity);

    /**
     * @param int $id
     * @return array
     */
    function fetchOne($id);

    /**
     * @param Criteria $Criteria
     * @return array
     */
    function fetchAll(Criteria $Criteria);

    function getName();

    /**
     * @return Schema
     */
    function getSchema();

    /**
     * @return Table
     */
    function getSchemaTable();

    /**
     * @param Table $Table
     * @return Table
     */
    function setSchemaTable(Table $Table);
}
