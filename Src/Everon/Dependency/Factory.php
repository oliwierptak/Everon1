<?php
namespace Everon\Dependency;


trait Factory
{

    /**
     * @var \Everon\Factory
     */
    protected $Factory = null;


    /**
     * @return \Everon\Interfaces\Factory
     */
    public function getFactory()
    {
        return $this->Factory;
    }

    /**
     * @param \Everon\Interfaces\Factory $Factory
     */
    public function setFactory(\Everon\Interfaces\Factory $Factory)
    {
        $this->Factory = $Factory;
    }

}
