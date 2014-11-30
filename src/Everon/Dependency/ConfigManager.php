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


trait ConfigManager
{
    /**
     * @var \Everon\Config\Manager
     */
    protected $ConfigManager = null;

    /**
     * @return \Everon\Config\Interfaces\Manager
     */
    public function getConfigManager()
    {
        return $this->ConfigManager;
    }

    /**
     * @param \Everon\Config\Interfaces\Manager $ConfigManager
     */
    public function setConfigManager(\Everon\Config\Interfaces\Manager $ConfigManager)
    {
        $this->ConfigManager = $ConfigManager;
    }
}