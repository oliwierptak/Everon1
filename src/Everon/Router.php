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

use Everon\Config\Interfaces\ItemRouter;

class Router implements Interfaces\Router
{
    use Dependency\Config;
    use Dependency\RequestValidator;

    use Helper\Arrays;
    use Helper\Asserts\IsArrayKey;
    use Helper\Regex;


    /**
     * @var ItemRouter
     */
    protected $CurrentRoute = null;


    /**
     * @param Interfaces\Config $Config
     * @param Interfaces\RequestValidator $Validator
     */
    public function __construct(Interfaces\Config $Config, Interfaces\RequestValidator $Validator)
    {
        $this->Config = $Config;
        $this->RequestValidator = $Validator;
    }

    /**
     * @inheritdoc
     */
    public function getRouteByRequest(Interfaces\Request $Request)
    {
        $DefaultItem = null;
        $this->CurrentRoute = null;

        if ($this->getConfig()->getItems() === null) {
            throw new Exception\Router('No routes defined');
        }

        foreach ($this->getConfig()->getItems() as $RouteItem) {
            /**
             * @var ItemRouter $RouteItem
             */
            if ($RouteItem->matchesByPath($Request->getPath())) {
                $this->CurrentRoute = $RouteItem;
                break;
            }
            
            //remember the first item as default
            $DefaultItem = ($this->CurrentRoute === null && $DefaultItem === null) ? $RouteItem : $DefaultItem;
        }

        //check for default route
        if ($Request->isEmptyUrl() && $this->CurrentRoute === null) {
            $DefaultItem = $this->getConfig()->getDefaultItem() ?: $DefaultItem;
            if ($DefaultItem === null) {
                throw new Exception\RouteNotDefined('Default route does not exist');
            }

            $this->CurrentRoute = $DefaultItem;
        }

        if ($this->CurrentRoute === null) {
            throw new Exception\RouteNotDefined($Request->getPath());
        }
        
        $this->validateAndUpdateRequestAndRouteItem($this->CurrentRoute, $Request);

        return $this->CurrentRoute;
    }

    /**
     * @inheritdoc
     */
    public function validateAndUpdateRequestAndRouteItem(ItemRouter $RouteItem, Interfaces\Request $Request)
    {
        list($query, $get, $post) = $this->getRequestValidator()->validate($RouteItem, $Request);
        
        $RouteItem->compileUrl($query);

        $query = $this->arrayMergeDefault($Request->getQueryCollection()->toArray(), $query);
        $Request->setQueryCollection($query);

        $get = $this->arrayMergeDefault($Request->getGetCollection()->toArray(), $get);
        $Request->setGetCollection($get);
        
        $post = $this->arrayMergeDefault($Request->getPostCollection()->toArray(), $post);
        $Request->setPostCollection(
            $post, $RouteItem->getAllowedTags()
        );
    }
    
    /**
     * @inheritdoc
     */
    public function getRouteByUrl($url)
    {
        /**
         * @var $RouteItem ItemRouter
         */
        foreach ($this->getConfig()->getItems() as $RouteItem) {
            if (strcasecmp($RouteItem->getParsedUrl(), $url) === 0) {
                return $RouteItem;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
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
     * @param \Everon\Config\Interfaces\ItemRouter $CurrentRoute
     */
    public function setCurrentRoute($CurrentRoute)
    {
        $this->CurrentRoute = $CurrentRoute;
    }

    /**
     * @return \Everon\Config\Interfaces\ItemRouter
     */
    public function getCurrentRoute()
    {
        return $this->CurrentRoute;
    }

    /**
     * @inheritdoc
     */
    public function getUrl($name, $query=[], $get=[])
    {
        $Item = $this->getConfig()->getItemByName($name);
        if ($Item === null) {
            throw new \Everon\Exception\Router('Invalid router item name for url: "%s"', (string) $name);
        }

        $Item->compileUrl($query);
        $url = $Item->getParsedUrl();

        $get_url = '';
        if (empty($get) === false) {
            $get_url = http_build_query($get);
            if (trim($get_url) !== '') {
                $get_url = '?'.$get_url;
            }
        }

        return $url.$get_url;
    }

}