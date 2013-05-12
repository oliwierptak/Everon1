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
    protected $ConfigManger = null;

    /**
     * @return \Everon\Config\Manager
     */
    public function getConfigManager()
    {
        return $this->ConfigManger;
    }

    public function setConfigManager(\Everon\Interfaces\ConfigManager $ConfigManager)
    {
        $this->ConfigManger = $ConfigManager;
    }

}