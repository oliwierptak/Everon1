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

interface Constraint extends Arrayable, Immutable
{
    /**
     * @param $name
     */
    function setName($name);

    /**
     * @return null
     */
    function getName();

    /**
     * @param null $table_name
     */
    function setTableName($table_name);

    /**
     * @return null
     */
    function getTableName();
}
