<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config;

use Everon\Dependency;
use Everon\Helper;
use Everon\Interfaces;

class Router extends \Everon\Config implements Interfaces\ConfigRouter
{
    use Dependency\Injection\Factory;
    
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;

    /**
     * @var array
     */
    protected $routes = null;

    /**
     * @var Interfaces\ConfigItemRouter
     */
    protected $DefaultRoute = null;


    protected function initRoutes()
    {
        $default_or_first_item = null;
        foreach ($this->getData() as $route_name => $config_data) {
            $config_data['route_name'] = $route_name;
            $RouteItem = $this->getFactory()->buildConfigItemRouter($config_data);
            $this->routes[$route_name] = $RouteItem;

            $default_or_first_item = (is_null($default_or_first_item)) ? $RouteItem : $default_or_first_item;
            if ($RouteItem->isDefault()) {
                $this->setDefaultRoute($RouteItem);
            }
        }

        if (is_null($this->DefaultRoute)) {
            $this->setDefaultRoute($default_or_first_item);
        }
    }

    /**
     * @param \Everon\Interfaces\ConfigItemRouter $RouteItem
     */
    public function setDefaultRoute(Interfaces\ConfigItemRouter $RouteItem)
    {
        $this->DefaultRoute = $RouteItem;
    }

    /**
     * @return Interfaces\ConfigItemRouter|null
     */
    public function getDefaultRoute()
    {
        if (is_null($this->DefaultRoute)) {
            $this->initRoutes();
        }

        return $this->DefaultRoute;
    }

    /**
     * @return array|null
     */
    public function getRoutes()
    {
        if (is_null($this->routes)) {
            $this->initRoutes();
        }

        return $this->routes;
    }

    /**
     * @param string $route_name
     * @return Interfaces\ConfigItemRouter
     */
    public function getRouteByName($route_name)
    {
        if (is_null($this->routes)) {
            $this->initRoutes();
        }

        $this->assertIsArrayKey($route_name, $this->routes, 'Invalid route name: "%s"');
        return $this->routes[$route_name];
    }

}
