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

interface Router
{
    /**
     * @return Interfaces\Router
     */
    function getRouter();

    /**
     * @param Interfaces\Router $Router
     */
    function setRouter(Interfaces\Router $Router);
}