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

use Everon\Interfaces;
use Everon\Exception;

interface Module extends Dependency\Factory
{
    /**
     * @return Interfaces\Config
     */
    function getConfig();

    /**
     * @param Interfaces\Config $Config
     */
    function setConfig(Interfaces\Config $Config);

    /**
     * @param $name
     * @return Interfaces\Controller
     */
    function getController($name);

    function getDirectory();

    /**
     * @param $directory
     */
    function setDirectory($directory);

    /**
     * @param Interfaces\FactoryWorker $FactoryWorker
     */
    function setFactoryWorker(Interfaces\FactoryWorker $FactoryWorker);

    /**
     * @return Interfaces\FactoryWorker
     */
    function getFactoryWorker();

    function getName();

    /**
     * @param $name
     */
    function setName($name);

    function setup();
}
