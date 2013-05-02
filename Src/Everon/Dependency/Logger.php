<?php
namespace Everon\Dependency;


trait Logger
{

    protected $Logger = null;


    /**
     * @return \Everon\Interfaces\logger
     */
    public function getLogger()
    {
        return $this->Logger;
    }

    public function setLogger(\Everon\Interfaces\Logger $Logger)
    {
        $this->Logger = $Logger;
    }

}
