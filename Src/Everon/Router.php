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

class Router implements Interfaces\Router
{
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
     * @param Interfaces\Config $Config
     * @param Interfaces\RouterValidator $Validator
     */
    public function __construct(Interfaces\Config $Config, Interfaces\RouterValidator $Validator)
    {
        $this->Config = $Config;
        $this->RouterValidator = $Validator;
    }

    /**
     * @param Interfaces\Request $Request
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\PageNotFound
     * @throws Exception\Router
     */
    public function getRouteByRequest(Interfaces\Request $Request)
    {
        $DefaultItem = null;
        $Item = null;

        foreach ($this->getConfig()->getItems() as $RouteItem) {
            /**
             * @var Interfaces\ConfigItemRouter $RouteItem
             */
            if ($RouteItem->matchesByUrl($Request->getUrl())) {
                $Item = $RouteItem;
                break;
            }
        }

        //check for default route
        if ($Request->isEmptyUrl() && $Item === null) {
            $DefaultItem = $this->getConfig()->getDefaultItem();
            if ($DefaultItem === null) {
                throw new Exception\PageNotFound('Default route does not exist');
            }
            
            $Item = $DefaultItem;
        }

        if ($Item !== null) {
            list($query, $get, $post) = $this->getRouterValidator()->validate($Item, $Request);

            $Request->setQueryCollection(
                array_merge($Request->getQueryCollection(), $query)
            );

            $Request->setGetCollection(
                array_merge($Request->getGetCollection(), $get)
            );

            $Request->setPostCollection(
                array_merge($Request->getPostCollection(), $post)
            );

            return $Item;
        }

        throw new Exception\PageNotFound($Request->getLocation());
    }
    
    /**
     * @param $url
     * @return Interfaces\ConfigItemRouter|null
     */
    public function getRouteByUrl($url)
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

}