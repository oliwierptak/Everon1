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

use Everon\Domain\Interfaces\Entity;
use Everon\Rest\Interfaces\Resource as ResourceInterface;

interface ResourceHandler
{

    /**
     * @param $version
     * @param $resource_name
     * @param array $data
     * @param int $user_id
     * @return ResourceInterface
     * @throws \Everon\Rest\Exception\Resource
     */
    function add($version, $resource_name, array $data, $user_id);

    /**
     * @param $version
     * @param $resource_name
     * @param $resource_id
     * @param array $data
     * @param int $user_id
     * @return ResourceInterface
     * @throws \Everon\Rest\Exception\Resource
     */
    function save($version, $resource_name, $resource_id, array $data, $user_id);

    /**
     * @param $version
     * @param $resource_name
     * @param $resource_id
     * @param int $user_id
     * @return ResourceInterface
     * @throws \Everon\Rest\Exception\Resource
     */
    function delete($version, $resource_name, $resource_id, $user_id);

    /**
     * @param Entity $Entity
     * @param $version
     * @param $resource_name
     * @return ResourceInterface
     */
    function buildResourceFromEntity(Entity $Entity, $version, $resource_name);
        
    /**
     * @param $resource_id
     * @param $resource_name
     * @param $version
     * @param ResourceNavigator $Navigator
     * @return ResourceInterface
     * @throws \Everon\Http\Exception\NotFound
     */
    function getResource($resource_id, $resource_name, $version, ResourceNavigator $Navigator);

    /**
     * @param $version
     * @param $resource_name
     * @return ResourceCollection
     * @param ResourceNavigator $Navigator
     * @throws \Exception
     */
    function getCollectionResource($version, $resource_name, ResourceNavigator $Navigator);

    /**
     * @param $resource_id
     * @param $name
     * @return mixed
     */
    function generateEntityId($resource_id, $name);

    /**
     * @param $entity_id
     * @param $name
     * @return string
     */
    function generateResourceId($entity_id, $name);

    /**
     * @param $resource_name
     * @param $resource_id
     * @param $collection
     * @param null $version
     * @return ResourceHref
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