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
    /**
     * @return string Eg. MySql
     */
    function getDriver();
    
    function getDatabase();
    
    function setPdoAdapter(PdoAdapter $PdoAdapter);

    /**
     * @return PdoAdapter
     */
    function getPdoAdapter();

    /**
     * @return array Returns [['table-name' => [...]], ['2nd-table-name' => [...]]]
     */
    function getTableList();

    /**
     * @return array Returns [['table-name' => [...]], ['2nd-table-name' => [...]]]
     */
    function getColumnList();

    /**
     * @return array Returns [['table-name' => [...]], ['2nd-table-name' => [...]]]
     */
    function getPrimaryKeysList();

    /**
     * @return array Returns [['table-name' => [...]], ['2nd-table-name' => [...]]]
     */
    function getForeignKeyList();

    /**
     * @return array Returns [['table-name' => [...]], ['2nd-table-name' => [...]]]
     */
    function getUniqueKeysList();
}
