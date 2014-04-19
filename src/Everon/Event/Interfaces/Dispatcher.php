<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event\Interfaces;

interface Dispatcher
{
    /**
     * @param $name
     * @param $Event Event
     */
    function dispatch($name, $Event);

    /**
     * @param $name
     * @param Listener $listener
     */
    function addListener($name, Listener $listener);

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
    public function countListeners($name);
}