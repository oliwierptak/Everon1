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


interface DataMapper extends \Everon\Interfaces\Dependency\Factory, \Everon\DataMapper\Interfaces\Dependency\Schema
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
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return bool
     */
    function deleteByCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return int
     */
    function count(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder=null);

    /**
     * @param int $id
     * @return array
     */
    function fetchOneById($id);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return array
     */
    function fetchOneByCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return array
     */
    function fetchAll(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @return string
     */
    function getName();

    /**
     * @return \Everon\DataMapper\Interfaces\Schema
     */
    function getSchema();

    /**
     * @return \Everon\DataMapper\Interfaces\Schema\Table
     */
    function getTable();

    /**
     * @param \Everon\DataMapper\Interfaces\Schema\Table $Table
     */
    function setTable(\Everon\DataMapper\Interfaces\Schema\Table $Table);

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
     * @return string
     */
    function getInsertSql();

    /**
     * @return string
     */
    function getUpdateSql();

    /**
     * @return string
     */
    function getDeleteSql();

    /**
     * @return string
     */
    function getFetchAllSql();

    /**
     * @param $select
     * @param $a
     * @param $b
     * @param $on_a
     * @param $on_b
     * @param string $type (left, right, ...)
     * @return string
     */
    function getJoinSql($select, $a, $b, $on_a, $on_b, $type='');

    /**
     * @return string
     */
    function getCountSql();

}