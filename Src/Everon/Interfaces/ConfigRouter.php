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


interface ConfigRouter
{
    function setDefaultRoute(ConfigItemRouter $RouteItem);
    function getDefaultRoute();

    /**
     * @return \array array of \Everon\ConfigItemRouter objects
     */
    function getRoutes();

    /**
     * @param string $route_name
     * @return \Everon\Config\Item\Router
     */
    function getRouteByName($route_name);
}