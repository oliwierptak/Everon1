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

use Everon\Interfaces\PdoAdapter;

interface Reader
{
    function getName();
    function setPdoAdapter(PdoAdapter $PdoAdapter);
    function getPdoAdapter();
    function getTableList();
    function getColumnList();
    function getConstraintList();
    function getForeignKeyList();
}
