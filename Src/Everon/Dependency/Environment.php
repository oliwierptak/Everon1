<?php
namespace Everon\Dependency;


trait Environment
{

    /**
     * @var \Everon\Interfaces\Environment
     */
    protected $Environment = null;


    /**
     * @return \Everon\Interfaces\Environment
     */
    public function getEnvironment()
    {
        return $this->Environment;
    }

    /**
     * @param \Everon\Interfaces\Environment $Environment
     */
    public function setEnvironment(\Everon\Interfaces\Environment $Environment)
    {
        $this->Environment = $Environment;
    }

}
