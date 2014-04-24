<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event\Interfaces;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
interface Manager
{
    /**
     * @param $event_name
     * @param callable $Callback
     * @param int $priority
     */
    function registerBefore($event_name, \Closure $Callback, $priority=0);

    /**
     * @param $event_name
     * @param callable $Callback
     * @param int $priority
     */
    function registerAfter($event_name, \Closure $Callback, $priority=0);
    
    /**
     * Resets the propagation to 'Running' and dispatches the event
     *
     * @param $event_name
     */
    function dispatchBefore($event_name);


    /**
     * @param $event_name
     */
    function dispatchAfter($event_name);
}