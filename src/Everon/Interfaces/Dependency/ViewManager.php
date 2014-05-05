<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Dependency;


interface ViewManager
{
    /**
     * @return \Everon\Interfaces\ViewManager
     */
    function getViewManager();

    /**
     * @param \Everon\Interfaces\ViewManager $Manager
     */
    function setViewManager(\Everon\Interfaces\ViewManager $Manager);
}