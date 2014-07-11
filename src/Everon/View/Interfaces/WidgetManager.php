<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Interfaces;


interface WidgetManager 
{
    /**
     * @param $name
     * @param string $namespace
     * @return View
     */
    function prepareView($name, $namespace='Everon\View\Widget');

    /**
     * @param $name
     * @param string $namespace
     * @return Widget
     */
    function createWidget($name, $namespace='Everon\View');

    /**
     * @param $name
     * @return string
     */
    function includeWidget($name);
}