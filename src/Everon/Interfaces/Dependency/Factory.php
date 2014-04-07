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

interface Factory
{
    /**
     * @return \Everon\Application\Interfaces\Factory
     */
    function getFactory();

    /**
     * @param \Everon\Application\Interfaces\Factory
     */
    function setFactory(\Everon\Application\Interfaces\Factory $Factory);
}