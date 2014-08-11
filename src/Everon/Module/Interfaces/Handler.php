<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Interfaces;

use Everon\Config;

interface Handler extends Config\Interfaces\Dependency\Manager
{
    /**
     * @param $name
     * @return Module
     */
    function getModuleByName($name);

    /**
     * @param $name
     * @param Module $Module
     */
    function setModuleByName($name, Module $Module);

    /**
     * @return array
     */
    function getPathsOfActiveModules();

    /**
     * @param $module_name
     * @return \Everon\Interfaces\FactoryWorker
     */
    function getFactoryWorker($module_name);

    function loadModuleDependencies();
}