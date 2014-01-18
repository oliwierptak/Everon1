<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Interfaces;

interface ForeignKeyInterface extends Interfaces\Arrayable, Interfaces\Immutable
{
    function setReferencedTableName($referenced_table_name);
    function getReferencedTableName();
    function setReferencedColumnName($referenced_column_name);
    function getReferencedColumnName();
}
