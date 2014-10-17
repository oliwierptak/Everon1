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

interface ForeignKey extends Constraint
{
    /**
     * @return string
     */
    function getColumnName();

    /**
     * @param string $referenced_table_name
     */
    function setForeignTableName($referenced_table_name);

    /**
     * @return string
     */
    function getForeignTableName();

    /**
     * @param string $referenced_column_name
     */
    function setForeignColumnName($referenced_column_name);

    /**
     * @return string
     */
    function getForeignColumnName();
    
    /**
     * @param null $referenced_schema_name
     */
    function setForeignSchemaName($referenced_schema_name);

    /**
     * @return null
     */
    function getForeignSchemaName();

    /**
     * @return string
     */
    function getForeignFullTableName();
}