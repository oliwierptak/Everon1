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
     * @param array $data
     * @return int Return last inserted ID
     */
    function add(array $data);

    /**
     * @param array $data
     * @return bool
     */
    function save(array $data);

    /**
     * @param Entity $Entity
     * @return bool
     */
    function delete(Entity $Entity);

    /**
     * @param int $id
     * @return array
     */
    function fetchOneById($id);

    /**
     * @param Criteria $Criteria
     * @return mixed
     */
    function fetchOneByCriteria(Criteria $Criteria);

    /**
     * @param Criteria $Criteria
     * @return array
     */
    function fetchAll(Criteria $Criteria);

    /**
     * @return string
     */
    function getName();

    /**
     * @param $data
     * @return mixed
     */
    function getIdFromData($data);

    /**
     * Validates all fields but ID. Assumes that ID has been checked elsewhere.
     *
     * @param array $data
     * @param bool $validate_id
     * @return array
     * @throws \Everon\Exception\DataMapper
     */
    function validateData(array $data, $validate_id);

    /**
     * @return Schema
     */
    function getSchema();

    /**
     * @return Table
     */
    function getTable();

    /**
     * @param Table $Table
     */
    function setTable(Table $Table);

}