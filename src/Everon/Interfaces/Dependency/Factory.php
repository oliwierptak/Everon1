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

interface Factory
{
    /**
     * @return Interfaces\Factory
     */
    function getFactory();

    /**
     * @param Interfaces\Factory $Factory
     */
    function setFactory(Interfaces\Factory $Factory);
}