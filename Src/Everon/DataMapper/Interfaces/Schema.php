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
    function getPdoAdapter($name);
    
    /**
     * @return ConnectionManager
     */
    function getConnectionManager();

    function getName();
    
    function getTables();

    function setTables($tables);

    function getTable($name);
}
