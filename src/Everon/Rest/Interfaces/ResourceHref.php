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
use Everon\Interfaces\Collection;


interface ResourceHref
{
    /**
     * @param string $version
     */
    public function setVersion($version);

    /**
     * @param $url
     */
    function setUrl($url);

    /**
     * @return string
     */
    function getResourceId();

    /**
     * @return string
     */
    function getResource();

    /**
     * @param string $versioning
     */
    function setVersioning($versioning);

    /**
     * @return string
     */
    function getVersioning();

    /**
     * @param $resource_id
     */
    function setResourceId($resource_id);

    /**
     * @return string
     */
    function getUrl();

    /**
     * @return string
     */
    public function getCollectionName();

    /**
     * @param $resource
     */
    function setResource($resource);

    /**
     * @param $collection
     */
    function setCollectionName($collection);

    /**
     * @return string
     */
    function getVersion();
}