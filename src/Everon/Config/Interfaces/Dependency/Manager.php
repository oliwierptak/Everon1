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

use Everon\Config\Interfaces;

interface Manager
{
    /**
     * @return Interfaces\Manager
     */
    function getConfigManager();

    /**
     * @param Interfaces\Manager $ConfigManager
     */
    function setConfigManager(Interfaces\Manager $ConfigManager);
}