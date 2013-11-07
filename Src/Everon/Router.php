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
     * @throws Exception\InvalidRoute
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
            
            //remember the first item as default
            $DefaultItem = ($Item === null && $DefaultItem === null) ? $RouteItem : $DefaultItem;
        }

        //check for default route
        if ($Request->isEmptyUrl() && $Item === null) {
            $DefaultItem = $this->getConfig()->getDefaultItem() ?: $DefaultItem;
            if ($DefaultItem === null) {
                throw new Exception\InvalidRoute('Default route does not exist');
            }
            
            $Item = $DefaultItem;
        }

        if ($Item === null) {
            throw new Exception\InvalidRoute($Request->getLocation());
        }
        
        $this->validateAndUpdateRequest($Item, $Request);

        return $Item;
    }

    /**
     * @param Interfaces\ConfigItemRouter $RouteItem
     * @param Interfaces\Request $Request
     */
    public function validateAndUpdateRequest(Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        list($query, $get, $post) = $this->getRouterValidator()->validate($RouteItem, $Request);

        $Request->setQueryCollection(
            array_merge($Request->getQueryCollection(), $query)
        );

        $Request->setGetCollection(
            array_merge($Request->getGetCollection(), $get)
        );

        $Request->setPostCollection(
            array_merge($Request->getPostCollection(), $post)
        );
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