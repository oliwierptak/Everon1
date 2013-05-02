<?php
namespace Everon\Dependency;


trait Response
{

    protected $Response = null;


    /**
     * @return \Everon\Interfaces\Response
     */
    public function getResponse()
    {
        return $this->Response;
    }

    /**
     * @param \Everon\Interfaces\Response $Response
     */
    public function setResponse(\Everon\Interfaces\Response $Response)
    {
        $this->Response = $Response;
    }

}
