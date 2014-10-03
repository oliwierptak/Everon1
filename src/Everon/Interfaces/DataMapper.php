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
     * @param $id
     * @return bool
     */
    function delete($id);

    /**
     * @param Criteria $Criteria
     * @return bool
     */
    function deleteByCriteria(Criteria $Criteria);

    /**
     * @param Criteria $Criteria
     * @return int
     */
    function count(Criteria $Criteria=null);

    /**
     * @param int $id
     * @return array
     */
    function fetchOneById($id);

    /**
     * @param Criteria $Criteria
     * @return array
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

    /**
     * @param string $read_connection_name
     */
    function setReadConnectionName($read_connection_name);

    /**
     * @return string
     */
    function getReadConnectionName();

    /**
     * @param string $write_connection_name
     */
    function setWriteConnectionName($write_connection_name);

    /**
     * @return string
     */
    function getWriteConnectionName();

    function beginTransaction();

    function commitTransaction();

    function rollbackTransaction();

    /**
     * @param array $data
     * @return array
     */
    function getInsertSql(array $data);

    /**
     * @param array $data
     * @internal param array $id
     * @return array
     */
    function getUpdateSql(array $data);

    /**
     * @param $id
     * @return array
     */
    function getDeleteSql($id);

    /**
     * @param Criteria $Criteria
     * @return array
     * @throws \Everon\Exception\DataMapper
     */
    function getDeleteByCriteriaSql(Criteria $Criteria);

    /**
     * @param Criteria $Criteria
     * @return array
     */
    function getFetchAllSql(Criteria $Criteria=null);

    /**
     * @param $select
     * @param $a
     * @param $b
     * @param $on_a
     * @param $on_b
     * @param Criteria $Criteria
     * @param string $type (left, right, ...)
     * @return string
     */
    function getJoinSql($select, $a, $b, $on_a, $on_b, Criteria $Criteria=null, $type='');

    /**
     * @param Criteria $Criteria
     * @return string
     */
    function getCountSql(Criteria $Criteria=null);

}