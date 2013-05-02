<?php
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