<?php
namespace Everon\Dependency;


trait RouterValidator
{

    protected $RouterValidator = null;


    /**
     * @return \Everon\Interfaces\RouterValidator
     */
    public function getRouterValidator()
    {
        return $this->RouterValidator;
    }

    /**
     * @param \Everon\Interfaces\RouterValidator $RouterValidator
     */
    public function setRouterValidator(\Everon\Interfaces\RouterValidator $RouterValidator)
    {
        $this->RouterValidator = $RouterValidator;
    }

}
