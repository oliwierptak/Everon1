<?php
namespace Everon\Config;

use Everon\Dependency;
use Everon\Helper;
use Everon\Interfaces;

class Router extends \Everon\Config implements Interfaces\RouterConfig
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\ConfigManager;
    
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;

    /**
     * @var array
     */
    protected $routes = null;

    /**
     * @var Interfaces\RouteItem
     */
    protected $DefaultRoute = null;


    protected function initRoutes()
    {
        $default_or_first_item = null;
        foreach ($this->getData() as $route_name => $config_data) {
            $config_data['route_name'] = $route_name;
            $RouteItem = $this->getFactory()->buildRouteItem($config_data);
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
     * @param \Everon\Interfaces\RouteItem $RouteItem
     */
    public function setDefaultRoute(Interfaces\RouteItem $RouteItem)
    {
        $this->DefaultRoute = $RouteItem;
    }

    /**
     * @return Interfaces\RouteItem|null
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
     * @param string $routeName
     * @return Interfaces\RouteItem
     */
    public function getRouteByName($routeName)
    {
        if (is_null($this->routes)) {
            $this->initRoutes();
        }

        $this->assertIsArrayKey($routeName, $this->routes, 'Invalid route name: "%s"');
        return $this->routes[$routeName];
    }

}
