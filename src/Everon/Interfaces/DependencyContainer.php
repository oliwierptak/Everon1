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


interface DependencyContainer
{
    /**
     * @param callable $Setup
     */
    function afterSetup(\Closure $Setup);

    /**
     * @param $class
     * @param $dependencies
     */
    function monitor($class, $dependencies);

    /**
     * @param $class_name
     * @return bool
     */
    function wantsFactory($class_name);

    /**
     * @inheritdoc
     */
    function register($name, \Closure $ServiceClosure);

    /**
     * Register only if not already registered
     *
     * @param $name
     * @param \Closure $ServiceClosure
     */
    function propose($name, \Closure $ServiceClosure);

    /**
     * @param $name
     * @return mixed
     * @throws \Everon\Exception\DependencyContainer
     */
    function resolve($name);

    /**
     * @param $class_name
     * @param $Receiver
     * @return mixed
     * @throws \Everon\Exception\DependencyContainer
     */
    function inject($class_name, $Receiver);

    /**
     * @return array
     */
    function getServices();

    /**
     * @return array
     */
    function getDefinitions();

    /**
     * @inheritdoc
     */
    function isRegistered($name);    
}
