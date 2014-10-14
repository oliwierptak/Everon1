<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

use Everon\Interfaces;

interface Controller extends 
    Interfaces\Controller, 
    Interfaces\Dependency\Factory, 
    \Everon\Rest\Interfaces\Dependency\ResourceManager,
    \Everon\Domain\Interfaces\Dependency\DomainManager
{
    function addResourceFromRequest();

    function saveResourceFromRequest();

    function deleteResourceFromRequest();

    function addResourceCollectionFromRequest();

    function saveResourceCollectionFromRequest();

    function deleteResourceCollectionFromRequest();

    /**
     * @return Interfaces\Resource
     */
    function getResourceFromRequest();

    function serveResourceFromRequest();

    function serveCollectionItemFromRequest();

    /**
     * @param \Exception $Exception
     */
    function showException(\Exception $Exception);

    /**
     * @return mixed
     */
    function getModel();

}
