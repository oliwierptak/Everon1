<?php
namespace Everon\Dependency;


trait Request
{

    protected $Request = null;


    /**
     * @return \Everon\Interfaces\Request
     */
    public function getRequest()
    {
        return $this->Request;
    }

    public function setRequest(\Everon\Interfaces\Request $Request)
    {
        $this->Request = $Request;
    }

}
