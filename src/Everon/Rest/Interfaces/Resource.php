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

interface Resource extends \Everon\Interfaces\Arrayable
{
    /**
     * @param $version
     */
    function setResourceVersion($version);

    /**
     * @inheritdoc
     */
    function getResourceVersion();

    /**
     * @param $href
     */
    function setResourceHref($href);

    /**
     * @inheritdoc
     */
    function getResourceHref();

    /**
     * @param $name
     */
    function setResourceName($name);

    /**
     * @inheritdoc
     */
    function getResourceName();

    /**
     * @return string
     */
    function toJson();
}