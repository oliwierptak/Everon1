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

interface Core
{

    /**
     * @param callable $ControllerIgniter
     * @param Interfaces\Response $Response
     * @throws Exception\InvalidRouterParameter|\Exception
     */    
    //function start(\Closure $ControllerIgniter, Interfaces\Response $Response);

    /**
     * @param Interfaces\Controller $Controller
     * @param $action
     * @return bool|mixed
     * @throws Exception\InvalidControllerMethod
     */
    //function run(Interfaces\Controller $Controller, $action);
    
    function run();

}
