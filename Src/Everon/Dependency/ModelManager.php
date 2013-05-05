<?php
namespace Everon\Dependency;


trait ModelManager
{

    protected $ModelManager = null;


    /**
     * @return \Everon\Interfaces\ModelManager
     */
    public function getModelManager()
    {
        return $this->ModelManager;
    }

    /**
     * @param \Everon\Interfaces\ModelManager $Manager
     */
    public function setModelManager(\Everon\Interfaces\ModelManager $Manager)
    {
        $this->ModelManager = $Manager;
    }

}
