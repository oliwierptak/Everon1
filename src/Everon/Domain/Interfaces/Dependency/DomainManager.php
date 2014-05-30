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

interface DomainManager
{
    /**
     * @param \Everon\Domain\Interfaces\Manager $DomainManager
     */
    function setDomainManager(\Everon\Domain\Interfaces\Manager $DomainManager);

    /**
     * @return \Everon\Domain\Interfaces\Manager
     */
    function getDomainManager();
}