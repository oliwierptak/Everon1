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

use Everon\Exception;

interface Controller
{
    function getName();

    /**
     * @param $name
     * @return mixed
     */
    function setName($name);

    /**
     * @param $action
     * @return mixed|void
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    function execute($action);
}
