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
     * @return Interfaces\Config
     */
    function getConfig();

    /**
     * @param Interfaces\Config $Config
     */
    function setConfig(Interfaces\Config $Config);

    /**
     * @param $name
     * @return Interfaces\Controller
     */
    function getController($name);

    function getDirectory();

    /**
     * @param $directory
     */
    function setDirectory($directory);

    function getName();

    /**
     * @param $name
     */
    function setName($name);

    /**
     * @return Interfaces\ConfigItemRouter
     */
    function getRouteConfig();

    /**
     * @param Interfaces\ConfigItemRouter $RouteConfig
     */
    function setRouteConfig(Interfaces\ConfigItemRouter $RouteConfig);

    /**
     * @param $name
     * @return Interfaces\View
     */
    function getView($name);
}
