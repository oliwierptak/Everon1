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

interface ResourceManager
{
    /**
     * @param $resource_id
     * @param $name
     * @param $version
     * @return mixed
     */
    function getResource($resource_id, $name, $version);
}