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

}