<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Interfaces\Dependency;

interface ModuleManager
{
    /**
     * @return \Everon\Module\Interfaces\Manager
     */
    function getModuleManager();

    /**
     * @param \Everon\Module\Interfaces\Manager $Manager
     */
    function setModuleManager(\Everon\Module\Interfaces\Manager $Manager);
}