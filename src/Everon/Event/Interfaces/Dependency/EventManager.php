<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event\Interfaces\Dependency;

use Everon\Event\Interfaces;

interface EventManager
{
    /**
     * @return Interfaces\Manager
     */
    function getEventManager();

    /**
     * @param Interfaces\Manager $EventManager
     * @return void
     */
    function setEventManager(Interfaces\Manager $EventManager);
}