<?php
namespace Everon;

/**
 * @property \Everon\Interfaces\ConfigRouter $Config
 * @method \Everon\Interfaces\ConfigRouter getConfig()
 */
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
     * @param Interfaces\ConfigRouter $Config
     */
    public function __construct(Interfaces\Request $Request, Interfaces\ConfigRouter $Config, Interfaces\RouterValidator $Validator)
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
            $RouteItem = $this->getConfig()->getDefaultRoute();
            $RouteItem = ($RouteItem) ?: $this->getRouteItemByUrl($Request->getUrl());
            
            if (is_null($RouteItem)) {
                $RouteItem = $this->getRouteItemByUrl($Request->getUrl());
            }

            if (is_null($RouteItem)) {
                throw new Exception\Router('Default route does not exist');
            }

            list($get, $post) = $this->getRouterValidator()->validate($RouteItem, $Request);
            $Request->setQueryCollection($get);
            $Request->setPostCollection($post);
            
            return $RouteItem;
        }

        foreach ($this->getConfig()->getRoutes() as $RouteItem) {
            /**
             * @var Interfaces\ConfigItemRouter $RouteItem
             */
            $request_url = $Request->getUrl();
            if ($RouteItem->matchesByUrl($request_url)) {
                list($get, $post) = $this->getRouterValidator()->validate($RouteItem, $Request);
                $Request->setQueryCollection($get);
                $Request->setPostCollection($post);
                
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
        foreach ($this->getConfig()->getRoutes() as $RouteItem) {
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
            return $this->getConfig()->getRouteByName($route_name);
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
/*        if (is_null($this->CurrentRoute)) {
            $this->initRoutes();
        }*/

        return $this->getCurrentRoute()->toArray();
    }

}