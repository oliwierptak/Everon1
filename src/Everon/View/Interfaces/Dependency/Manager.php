<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Interfaces\Dependency;


interface Manager
{
    /**
     * @return \Everon\View\Interfaces\Manager
     */
    function getViewManager();

    /**
     * @param \Everon\View\Interfaces\Manager $Manager
     */
    function setViewManager(\Everon\View\Interfaces\Manager $Manager);
}