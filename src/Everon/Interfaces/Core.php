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
use Everon\RequestIdentifier;

interface Core extends 
    Dependency\Logger,
    Dependency\Factory,
    Dependency\Response,
    Dependency\Request,
    Dependency\Router,
    \Everon\Config\Interfaces\Dependency\Manager,
    \Everon\Module\Interfaces\Dependency\ModuleManager
{
    function getRequestIdentifier();

    /**
     * @inheritdoc
     */
    function run(RequestIdentifier $RequestIdentifier);
    
    function shutdown();

    /**
     * @param \Exception $Exception
     */
    function handleExceptions(\Exception $Exception);

    /**
     * @param Interfaces\Controller $Controller
     */
    function setController(\Everon\Interfaces\Controller $Controller);

    /**
     * @return Interfaces\Controller
     */
    function getController();

    function terminate();

    /**
     * @param $name
     * @param array $query
     * @param array $get
     */
    function redirectAndTerminate($name, $query=[], $get=[]);
}
