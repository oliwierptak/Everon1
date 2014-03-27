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

interface ClassLoader
{
    /**
     * @param bool $prepend
     */
    function register($prepend);
    function unRegister();

    /**
     * @param $namespace
     * @param $directory
     */
    function add($namespace, $directory);

    /**
     * @param $class_name
     * @return mixed|null|string
     * @throws \RuntimeException
     */
    function load($class_name);
}
