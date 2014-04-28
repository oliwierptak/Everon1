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
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
interface Manager
{
    /**
     * @param $event_name
     * @param Context $Context
     * @param int $priority
     */
    function registerBefore($event_name, Context $Context, $priority=1);

    /**
     * @param $event_name
     * @param Context $Callback
     * @param int $priority
     */
    function registerAfter($event_name, Context $Callback, $priority=1);
    
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

    /**
     * @param array $listeners
     */
    function setEvents(array $listeners);

    /**
     * @return array
     */
    function getEvents();
}