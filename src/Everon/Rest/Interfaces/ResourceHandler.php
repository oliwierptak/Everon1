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
     * @param $name
     * @param $version
     * @return Resource
     * @throws \Everon\Http\Exception\NotFound
     */
    function getResource($resource_id, $name, $version);

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
     * @param $resource_id
     * @param $name
     * @return string
     */
    function getResourceUrl($resource_id, $name);
    
    function getUrl();
}