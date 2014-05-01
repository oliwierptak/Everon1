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
interface Context
{
    /**
     * @param \Closure $Callback
     */
    function setCallback(\Closure $Callback);

    /**
     * @return \Closure
     */
    function getCallback();

    /**
     * @param mixed $Callee
     */
    function setCaller($Callee);

    /**
     * @return mixed
     */
    function getCaller();

    /**
     * @return mixed
     */
    function execute();
}