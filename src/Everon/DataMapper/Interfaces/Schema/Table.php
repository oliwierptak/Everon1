<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Schema;

use Everon\Interfaces\Arrayable;
use Everon\Interfaces\Immutable;

interface Table extends Arrayable, Immutable
{
    function getName();

    function getSchema();

    function getFullName();

    /**
     * @return array
     */
    function getColumns();

    /**
     * @param $name
     * @return Column
     * @throws \Everon\DataMapper\Exception\Table
     */
    function getColumnByName($name);
    
    /**
     * @return array
     */
    function getForeignKeys();
    
    /**
     * @return array
     */
    function getPrimaryKeys();

    /**
     * @param $name
     * @return mixed
     * @throws \Everon\DataMapper\Exception\Table
     */
    function getPrimaryKeyByName($name);

    /**
     * @return array
     */
    function getUniqueKeys();

    function getPk();

    /**
     * @param $id
     * @return mixed
     */
    function validateId($id);

    /**
     * @param array $data
     * @param $validate_id
     * @return array
     */
    function validateData(array $data, $validate_id);

    /**
     * @param $data
     * @return mixed
     */
    function getIdFromData($data);

}
