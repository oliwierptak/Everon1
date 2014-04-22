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
interface Dispatcher
{
    /**
     * @param $controllerName
     */
    function dispatch($controllerName);

    /**
     * Example call to listen for events on a certain function of a class (initial thought):
     * <p>
     * $this->getEventManager()->register(UserEvent::register, 'User', 'Login', 'login', function() {
     *      //Do something
     * }
     * </p>
     * @param $eventName
     * @param Listener $listener
     * @param $moduleName
     * @param $controllerName
     * @param $action
     * @param $callback
     */
    function register($eventName, Listener $listener, $moduleName, $controllerName, $action, $callback);

    /**
     * @param $name
     * @throws \Everon\Exception\InvalidListener
     */
    function removeListener($name);

    /**
     * @param null $name
     * @return array
     */
    function getListeners($name = null);

    /**
     * @return bool
     */
    function hasListeners();

    /**
     * @param $name
     * @return int
     */
    function countListeners($name);
}