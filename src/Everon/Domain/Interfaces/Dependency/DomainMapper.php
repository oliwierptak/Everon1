<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Interfaces\Dependency;

interface DomainMapper
{
    /**
     * @param \Everon\Domain\Interfaces\Mapper $DomainMapper
     */
    function setDomainMapper(\Everon\Domain\Interfaces\Mapper $DomainMapper);

    /**
     * @return \Everon\Domain\Interfaces\Mapper
     */
    function getDomainMapper();
}