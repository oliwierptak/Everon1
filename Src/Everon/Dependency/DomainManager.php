<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;


trait DomainManager
{

    protected $DomainManager = null;


    /**
     * @return \Everon\Interfaces\DomainManager
     */
    public function getDomainManager()
    {
        return $this->DomainManager;
    }

    /**
     * @param \Everon\Interfaces\DomainManager $Manager
     */
    public function setDomainManager(\Everon\Interfaces\DomainManager $Manager)
    {
        $this->DomainManager = $Manager;
    }

}
