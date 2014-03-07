<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Dependency;

use Everon\Interfaces;

interface Request
{
    /**
     * @return Interfaces\Request
     */
    function getRequest();

    /**
     * @param Interfaces\Request $Request
     * @return void
     */
    function setRequest(Interfaces\Request $Request);
}