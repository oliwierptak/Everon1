<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Interfaces;
use Everon\Exception;

interface Module
{
    /**
     * @return null
     */
    function getName();

    /**
     * @param null $name
     */
    function setName($name);

    /**
     * @return Interfaces\ConfigItem
     */
    function getModuleConfig();

    /**
     * @param Interfaces\ConfigItem $ModuleConfig
     */
    function setModuleConfig(Interfaces\ConfigItem $ModuleConfig);

    /**
     * @return Interfaces\ConfigItemRouter
     */
    function getRouteConfig();

    /**
     * @param Interfaces\ConfigItemRouter $RouteConfig
     */
    function setRouteConfig(Interfaces\ConfigItemRouter $RouteConfig);
}
