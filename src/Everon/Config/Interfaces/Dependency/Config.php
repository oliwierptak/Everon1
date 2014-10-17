<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces\Dependency;

interface Config
{
    /**
     * @return \Everon\Interfaces\Config
     */
    function getConfig();

    /**
     * @param \Everon\Interfaces\Config $Config
     */
    function setConfig(\Everon\Interfaces\Config $Config);
}