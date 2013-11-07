<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Mvc;

use Everon\Dependency;
use Everon\Helper;
use Everon\Exception;
use Everon\Interfaces;
use Everon\Http;
use Everon\Router as EveronRouter;

class Router extends EveronRouter implements Interfaces\Router
{
    /**
     * @param Interfaces\Request $Request
     * @return Interfaces\ConfigItemRouter
     * @throws Exception\InvalidRoute
     * @throws Http\Exception\NotFound
     */
    public function getRouteByRequest(Interfaces\Request $Request)
    {
        try {
            return parent::getRouteByRequest($Request);
        }
        catch (Exception\InvalidRoute $e) {
            throw new Http\Exception\NotFound('Page not found: '.$e->getMessage(), null);
        }
    }

}