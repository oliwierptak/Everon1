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


interface Router
{
    /**
     * @param \Everon\Interfaces\Request $Request
     * @return \Everon\Interfaces\ConfigItemRouter
     * @throws \Everon\Exception\InvalidRoute
     */
    function getRouteByRequest(Request $Request);

    /**
     * @param $route_name
     * @return \Everon\Interfaces\ConfigItemRouter
     * @throws \Everon\Exception\Router
     */
    function getRouteByName($route_name);

    /**
     * @param $url
     * @return \Everon\Interfaces\ConfigItemRouter|null
     */
    function getRouteByUrl($url);

    /**
     * @param \Everon\Interfaces\Config $Config
     * @return void
     */
    function setConfig(Config $Config);

    /**
     * @return \Everon\Interfaces\Config
     */
    function getConfig();
}