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
    function wantsFactory($class_name);
    function register($name, \Closure $ServiceClosure);
    function resolve($name);
    function inject($class_name, $Receiver);
    function getServices();
    function getDefinitions();
    function isRegistered($name);    
}
