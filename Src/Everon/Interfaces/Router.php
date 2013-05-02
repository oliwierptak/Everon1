<?php
namespace Everon\Interfaces;


interface Router
{
    /**
     * @param \Everon\Interfaces\Request $Request
     * @return \Everon\Interfaces\RouteItem
     * @throws \Everon\Exception\PageNotFound
     */
    function getRouteItemByRequest(Request $Request);

    /**
     * @param \Everon\Interfaces\RouteItem $RouteItem
     * @param \Everon\Interfaces\Request $Request
     * @return void
     */
    function setRequestPostDataAndValidateRoute(RouteItem $RouteItem, Request $Request);

    /**
     * @param $route_name
     * @return \Everon\Interfaces\RouteItem
     * @throws \Everon\Exception\Router
     */
    function getRouteByName($route_name);

    /**
     * @param $url
     * @return \Everon\Interfaces\RouteItem|null
     */
    function getRouteItemByUrl($url);

    /**
     * @param $route_name
     * @param array $route_params
     * @param array $request_params
     * @return void
     */
    function validateRoute($route_name, array $route_params, array $request_params);
        
    /**
     * @param \Everon\Interfaces\Config $Config
     * @return void
     */
    function setConfig(Config $Config);

    /**
     * @return \Everon\Interfaces\Config
     */
    function getConfig();

    /**
     * @param \Everon\Interfaces\Request $Request
     * @return void
     */
    function setRequest(Request $Request);

    /**
     * @return \Everon\Interfaces\Request
     */
    function getRequest();

    /**
     * @param \Everon\Interfaces\RouteItem $RouteItem
     * @return void
     */
    function setCurrentRoute(RouteItem $RouteItem);

    /**
     * @return \Everon\Interfaces\RouteItem
     */
    function getCurrentRoute();

}