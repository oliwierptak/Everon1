<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

interface Collection extends Arrayable
{
    /**
     * @param $name
     * @return bool
     */
    function has($name);

    /**
     * @param $name
     */
    function remove($name);

    /**
     * @param $name
     * @param $value
     */
    function set($name, $value);

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    function get($name, $default=null);

    /**
     * @return bool
     */
    function isEmpty();
}