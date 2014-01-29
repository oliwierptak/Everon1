<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Dependency;


trait ConnectionManager
{
    /**
     * @var \Everon\DataMapper\Interfaces\ConnectionManager
     */
    protected $ConnectionManager = null;

    /**
     * @return \Everon\DataMapper\Interfaces\ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->ConnectionManager;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\ConnectionManager $ConnectionManager
     */
    public function setConnectionManager(\Everon\DataMapper\Interfaces\ConnectionManager $ConnectionManager)
    {
        $this->ConnectionManager = $ConnectionManager;
    }
}