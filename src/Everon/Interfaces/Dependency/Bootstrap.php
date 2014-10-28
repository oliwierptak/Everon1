<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Dependency;

interface Bootstrap
{
    /**
     * @return \Everon\Interfaces\Bootstrap
     */
    function getBootstrap();

    /**
     * @param \Everon\Interfaces\Bootstrap
     */
    function setBootstrap(\Everon\Interfaces\Bootstrap $Bootstrap);
}