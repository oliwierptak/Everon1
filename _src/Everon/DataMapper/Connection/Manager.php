<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Connection;

use Everon\DataMapper\Connection;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces;

class Manager implements Interfaces\ConnectionManager
{
    protected $connections = null;

    /**
     * @param array $connections
     */
    public function __construct(array $connections)
    {
        $this->connections = $connections;
    }

    /**
     * @inheritdoc
     */
    public function add($name, Interfaces\ConnectionItem $ConnectionItem)
    {
        $this->connections[$name] = $ConnectionItem;
    }

    /**
     * @inheritdoc
     */    
    public function remove($name)
    {
        unset($this->connections[$name]);
    }

    /**
     * @inheritdoc
     */    
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * @inheritdoc
     */
    public function getConnectionByName($name)
    {
        if (array_key_exists($name, $this->connections) === false) {
            throw new Exception\ConnectionManager('Invalid connection name: "%s"', $name);
        }
        
        return $this->connections[$name];
    }
}