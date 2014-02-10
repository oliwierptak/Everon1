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


trait ModuleManager
{
    /**
     * @var \Everon\Interfaces\ModuleManager
     */
    protected $ModuleManager = null;


    /**
     * @return \Everon\Interfaces\ModuleManager
     */
    public function getModuleManager()
    {
        return $this->ModuleManager;
    }

    /**
     * @param \Everon\Interfaces\ModuleManager
     */
    public function setModuleManager(\Everon\Interfaces\ModuleManager $Manager)
    {
        $this->ModuleManager = $Manager;
    }

}
