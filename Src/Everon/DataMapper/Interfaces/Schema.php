<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

use Everon\Interfaces\PdoAdapter;

interface Schema
{
    /**
     * @param $name
     * @return PdoAdapter
     */    
    function getPdoAdapterByName($name);
    
    /**
     * @return ConnectionManager
     */
    function getConnectionManager();

    /**
     * @return string MySql
     */
    function getDriver();

    function getDatabase();
    
    function getTables();

    function setTables($tables);

    /**
     * @param $name
     * @return Schema\Table
     */
    function getTable($name);
}
