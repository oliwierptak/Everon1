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

use Everon\Config;

interface ConfigManager
{
    /**
     * @return Config\Interfaces\Manager
     */
    function getConfigManager();

    /**
     * @param Config\Interfaces\Manager $ConfigManager
     */
    function setConfigManager(Config\Interfaces\Manager $ConfigManager);
}