<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Dependency;

use Everon\DataMapper\Interfaces;

interface DataMapperManager
{
    /**
     * @return Interfaces\Manager
     */
    function getDataMapperManager();

    /**
     * @param Interfaces\Manager $Manager
     */
    function setDataMapperManager(Interfaces\Manager $Manager);
}