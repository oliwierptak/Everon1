<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Dependency;

interface Schema
{
    /**
     * @return \Everon\DataMapper\Interfaces\Schema
     */
    function getSchema();

    /**
     * @param \Everon\DataMapper\Interfaces\Schema
     */
    function setSchema(\Everon\DataMapper\Interfaces\Schema $Schema);
}