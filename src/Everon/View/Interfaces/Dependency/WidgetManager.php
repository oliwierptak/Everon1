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


interface WidgetManager
{
    /**
     * @return \Everon\View\Interfaces\WidgetManager
     */
    function getViewWidgetManager();

    /**
     * @param \Everon\View\Interfaces\WidgetManager $Manager
     */
    function setViewWidgetManager(\Everon\View\Interfaces\WidgetManager $Manager);
}