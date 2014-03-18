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

interface Client
{
    /**
     * @param $resource_name
     * @param $resource_id
     * @param $collection_name
     */
    function get($resource_name, $resource_id=null, $collection_name=null);

    /**
     * @param $resource_name
     * @param array $data
     */
    function post($resource_name, array $data);

    /**
     * @param $resource_name
     * @param $resource_id
     * @param array $data
     */
    function put($resource_name, $resource_id, array $data);

    /**
     * @param $resource_name
     * @param $resource_id
     */
    function delete($resource_name, $resource_id);
    
    /**
     * @param $resource_name
     * @param $resource_id
     * @param $collection
     * @return string
     */
    function getUrl($resource_name, $resource_id=null, $collection=null);

    /**
     * @param CurlAdapter $CurlAdapter
     */
    function setCurlAdapter($CurlAdapter);

    /**
     * @return CurlAdapter
     */
    function getCurlAdapter();

    /**
     * @param ResourceHref $ResourceHref
     */
    function setResourceHref($ResourceHref);

    /**
     * @return ResourceHref
     */
    function getResourceHref();
}