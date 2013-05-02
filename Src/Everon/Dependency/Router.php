<?php
namespace Everon\Dependency;


trait Router
{

    protected $Router = null;


    /**
     * @return \Everon\Interfaces\Router
     */
    public function getRouter()
    {
        return $this->Router;
    }

    public function setRouter(\Everon\Interfaces\Router $Router)
    {
        $this->Router = $Router;
    }

}
