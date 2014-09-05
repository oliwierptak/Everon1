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

interface Core
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
     * @inheritdoc
     */

    /**
     * @param Interfaces\Controller $Controller
     */
    function setController(\Everon\Interfaces\Controller $Controller);

    /**
     * @return Interfaces\Controller
     */
    function getController();

    function terminate();
}
