<?php
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