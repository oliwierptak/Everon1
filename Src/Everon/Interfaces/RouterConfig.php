<?php
namespace Everon\Interfaces;


interface RouterConfig
{
    function setDefaultRoute(RouteItem $RouteItem);
    function getDefaultRoute();

    /**
     * Returns array of RouteItem objects
     *
     * @return \array array of \Everon\RouteItem objects
     */
    function getRoutes();

    /**
     * @param string $routeName The route name
     * @return \Everon\RouteItem RouteItem object
     */
    function getRouteByName($routeName);
}