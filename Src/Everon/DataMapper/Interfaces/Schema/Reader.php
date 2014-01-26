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

interface Reader
{
    function getName();
    function setPdo(\PDO $Pdo);
    function getPdo();
    function getTableList();
    function getColumnList();
    function getConstraintList();
    function getForeignKeyList();
    function getColumnsForTable($table_name);
    function getConstraintsForTable($table_name);
    function getForeignKeysForTable($table_name);
}
