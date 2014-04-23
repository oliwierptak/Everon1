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
     * @param $moduleName
     * @param $controllerName
     * @param $action
     */
    function dispatch($moduleName, $controllerName, $action);

    /**
     * @param $moduleName
     * @param $controllerName
     * @param $action
     * @param $beforeExecuteCallback
     * @param $afterExecuteCallback
     * @throws \Everon\Exception\Helper
     */
    function register($moduleName, $controllerName, $action, $beforeExecuteCallback, $afterExecuteCallback);
    /**
     * <p>
     * Resets the propagation to 'Running' and dispatches the event
     * </p>
     *
     * @param $moduleName
     * @param $controllerName
     * @param $action
     */
    function dispatchBeforeExecute($moduleName,$controllerName, $action);

    /**
     * @param $moduleName
     * @param $controllerName
     * @param $action
     */
    function dispatchAfterExecute($moduleName,$controllerName, $action);
}