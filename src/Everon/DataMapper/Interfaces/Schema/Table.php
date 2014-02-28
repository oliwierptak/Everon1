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

    /**
     * @return array
     */
    function getColumns();
    
    /**
     * @return array
     */
    function getForeignKeys();
    
    /**
     * @return array
     */
    function getPrimaryKeys();

    /**
     * @return array
     */
    function getUniqueKeys();

    function getPk();

    /**
     * @param $id
     * @return mixed
     * @throws \Everon\DataMapper\Exception\Column
     */
    function validateId($id);
}
