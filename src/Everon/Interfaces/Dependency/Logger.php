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

interface Logger
{
    /**
     * @return Interfaces\Logger
     */
    function getLogger();

    /**
     * @param Interfaces\Logger $Logger
     * @return void
     */
    function setLogger(Interfaces\Logger $Logger);
}