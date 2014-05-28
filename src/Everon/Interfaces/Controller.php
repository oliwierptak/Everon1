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

use Everon\Config\Interfaces\ItemRouter;
use Everon\Exception;
use Everon\Http;

/**
 * @method Response getResponse()
 * @method void setResponse(Response $Response)
 */
interface Controller
{
    function getName();

    /**
     * @param $name
     * @return mixed
     */
    function setName($name);

    /**
     * @param $action
     * @return void
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    function execute($action);


    /**
     * @param $name
     * @param array $query
     * @param array $get
     * @return string
     * @throws Exception\Controller
     */
    function getUrl($name, $query=[], $get=[]);

    /**
     * @param ItemRouter $CurrentRoute
     */
    function setCurrentRoute(ItemRouter $CurrentRoute);

    /**
     * @return ItemRouter
     */
    function getCurrentRoute();

    /**
     * @param \Exception $Exception
     */
    function showException(\Exception $Exception);
}
