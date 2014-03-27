<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Dependency;


trait Manager
{
    /**
     * @var \Everon\Module\Interfaces\Manager
     */
    protected $ModuleManager = null;


    /**
     * @return \Everon\Module\Interfaces\Manager
     */
    public function getModuleManager()
    {
        return $this->ModuleManager;
    }

    /**
     * @param \Everon\Module\Interfaces\Manager $Manager
     */
    public function setModuleManager(\Everon\Module\Interfaces\Manager $Manager)
    {
        $this->ModuleManager = $Manager;
    }

}
