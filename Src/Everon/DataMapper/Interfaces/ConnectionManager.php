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

use Everon\DataMapper\Interfaces;
use Everon\DataMapper\Exception;

interface ConnectionManager
{
    /**
     * @param $name
     * @param array $data
     */
    function add($name, array $data);

    /**
     * @param $name
     */
    function remove($name);

    /**
     * @return array
     */
    function getConnections();
    
    /**
     * @param $name
     * @return Interfaces\ConnectionItem
     * @throws Exception\ConnectionManager
     */    
    function getConnectionByName($name);
}
