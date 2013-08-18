<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

class Router implements Interfaces\Router, Interfaces\Arrayable
{
    use Dependency\Request;
    use Dependency\Config;
    use Dependency\RouterValidator;

    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
    use Helper\Regex;

    /**
     * @var Interfaces\ConfigItemRouter
     */
    protected $CurrentRoute = null;


    /**
     * @param Interfaces\Request $Request
     * @param Interfaces\Config $Config
     * @param Interfaces\RouterValidator $Validator
     */
    public function __construct(Interfaces\Request $Request, Interfaces\Config $Config, Interfaces\RouterValidator $Validator)
    {
        $this->Request = $Request;
        $this->Config = $Config;
        $this->RouterValidator = $Validator;
    }

    protected function initRoutes()
    {
        $this->setCurrentRoute(
            $this->getRouteItemByRequest($this->getRequest())
        );
    }

    /**
     * @param Interfaces\Request $Request
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\PageNotFound
     * @throws Exception\Router
     */
    public function getRouteItemByRequest(Interfaces\Request $Request)
    {
        $RouteItem = null;
        $url_tokens = $Request->getUrlAsTokens();

        if (count($url_tokens) == 0) {//empty url @todo: refactor into method, remove this comment
            $RouteItem = $this->getConfig()->getDefaultItem();
            $RouteItem = ($RouteItem) ?: $this->getRouteItemByUrl($Request->getUrl());
            
            if (is_null($RouteItem)) {
                $RouteItem = $this->getRouteItemByUrl($Request->getUrl());
            }

            if (is_null($RouteItem)) {
                throw new Exception\Router('Default route does not exist');
            }

            list($get, $post) = $this->getRouterValidator()->validate($RouteItem, $Request);
            
            $Request->setQueryCollection(
                array_merge($Request->getQueryCollection(), $get)
            );

            $Request->setPostCollection(
                array_merge($Request->getPostCollection(), $post)
            );
            
            return $RouteItem;
        }

        foreach ($this->getConfig()->getItems() as $RouteItem) {
            /**
             * @var Interfaces\ConfigItemRouter $RouteItem
             */
            $request_url = $Request->getUrl();
            if ($RouteItem->matchesByUrl($request_url)) {
                list($get, $post) = $this->getRouterValidator()->validate($RouteItem, $Request);
                $Request->setQueryCollection(
                    array_merge($Request->getQueryCollection(), $get)
                );

                $Request->setPostCollection(
                    array_merge($Request->getPostCollection(), $post)
                );
                
                return $RouteItem;
            }
        }

        throw new Exception\PageNotFound($Request->getLocation());
    }

    /**
     * @param $url
     * @return Interfaces\ConfigItemRouter|null
     */
    public function getRouteItemByUrl($url)
    {
        /**
         * @var $RouteItem Interfaces\ConfigItemRouter
         */
        foreach ($this->getConfig()->getItems() as $RouteItem) {
            if (strcasecmp($RouteItem->getUrl(), $url) === 0) {
                return $RouteItem;
            }
        }

        return null;
    }

    /**
     * @param $route_name
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\Router
     */
    public function getRouteByName($route_name)
    {
        try {
            return $this->getConfig()->getItemByName($route_name);
        }
        catch (\Exception $e) {
            throw new Exception\Router($e);
        }
    }

    /**
     * @param Interfaces\ConfigItemRouter $RouteItem
     */
    public function setCurrentRoute(Interfaces\ConfigItemRouter $RouteItem)
    {
        $this->CurrentRoute = $RouteItem;
    }

    /**
     * @return Interfaces\ConfigItemRouter|null
     */
    public function getCurrentRoute()
    {
        if (is_null($this->CurrentRoute)) {
            $this->initRoutes();
        }

        return $this->CurrentRoute;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getCurrentRoute()->toArray();
    }

}