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
     * @param $eventName
     * @param $when
     */
    function dispatch($eventName, $when);

    /**
     * @param $eventName
     * @param $beforeExecuteCallback
     * @param $afterExecuteCallback
     * @throws \Everon\Exception\Helper
     */
    function register($eventName, $beforeExecuteCallback, $afterExecuteCallback);
    /**
     * <p>
     * Resets the propagation to 'Running' and dispatches the event
     * </p>
     *
     * @param $eventName
     */
    function dispatchBeforeExecute($eventName);


    /**
     * @param $eventName
     */
    function dispatchAfterExecute($eventName);
}