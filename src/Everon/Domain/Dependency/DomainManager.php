<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Dependency;


trait DomainManager
{
    /**
     * @var \Everon\Domain\Interfaces\Manager
     */
    protected $DomainManager = null;


    /**
     * @return \Everon\Domain\Interfaces\Manager
     */
    public function getDomainManager()
    {
        return $this->DomainManager;
    }

    /**
     * @param \Everon\Domain\Interfaces\Manager
     */
    public function setDomainManager(\Everon\Domain\Interfaces\Manager $Manager)
    {
        $this->DomainManager = $Manager;
    }

}
