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
use Everon\Interfaces;
use Everon\Http;

/**
 * @method Interfaces\Response getResponse()
 * @method void setResponse(Interfaces\Response $Response)
 */
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
     * @return void
     * @throws Exception\InvalidControllerMethod
     * @throws Exception\InvalidControllerResponse
     */
    function execute($action);

}
