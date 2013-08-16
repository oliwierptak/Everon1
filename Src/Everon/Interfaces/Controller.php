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

interface Controller
{
    function getName();

    /**
     * @param $name
     * @return mixed
     */
    function setName($name);

    /**
     * @param $result
     * @param Interfaces\Response $Response
     * @return Interfaces\Response
     */
    function result($result, Interfaces\Response $Response);

    /**
     * @param $action
     * @return mixed
     * @throws \Everon\Exception\InvalidControllerMethod
     */
    function execute($action);
}
