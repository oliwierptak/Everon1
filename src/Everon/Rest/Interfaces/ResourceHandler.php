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


interface ResourceHandler
{
    /**
     * @param $resource_id
     * @param $resource_name
     * @param $version
     * @param ResourceNavigator $Navigator
     * @return Resource
     * @throws \Everon\Http\Exception\NotFound
     */
    function getResource($resource_id, $resource_name, $version, ResourceNavigator $Navigator);

    /**
     * @param $name
     * @param $version
     * @return ResourceCollection
     * @param ResourceNavigator $Navigator
     * @throws \Exception
     */
    function getCollectionResource($name, $version, ResourceNavigator $Navigator);

    /**
     * @param $resource_id
     * @param $name
     * @return mixed
     */
    function generateEntityId($resource_id, $name);

    /**
     * @param $entity_id
     * @param $name
     * @return mixed
     */
    function generateResourceId($entity_id, $name);

    /**
     * @param $resource_name
     * @param $resource_id
     * @param $collection
     * @param null $version
     * @return string
     */
    function getResourceUrl($resource_name, $resource_id, $collection, $version=null);

    /**
     * @param $resource_name
     * @return string
     * @throws \Everon\Rest\Exception\Manager
     */
    function getDomainNameFromMapping($resource_name);
    
    /**
     * @param $domain_name
     * @return string
     * @throws \Everon\Rest\Exception\Manager
     */
    function getResourceNameFromMapping($domain_name);
}