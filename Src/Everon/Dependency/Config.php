<?php
namespace Everon\Dependency;


trait Config
{

    protected $Config = null;


    /**
     * @return \Everon\Interfaces\Config
     */
    public function getConfig()
    {
        return $this->Config;
    }

    public function setConfig(\Everon\Interfaces\Config $Config)
    {
        $this->Config = $Config;
    }

}
