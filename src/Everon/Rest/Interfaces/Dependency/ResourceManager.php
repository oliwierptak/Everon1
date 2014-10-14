<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces\Dependency;

use Everon\Rest\Interfaces;

interface ResourceManager
{
    /**
     * @return Interfaces\ResourceManager
     */
    function getResourceManager();

    /**
     * @param Interfaces\ResourceManager $ResourceManager
     */
    function setResourceManager(\Everon\Rest\Interfaces\ResourceManager $ResourceManager);
}