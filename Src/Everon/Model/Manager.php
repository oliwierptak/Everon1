<?php
namespace Everon\Model;

use Everon\Dependency;
use Everon\Interfaces;

abstract class Manager implements Interfaces\ModelManager
{
    use Dependency\Injection\Factory;

    /**
     * List of Models
     * @var array
     */
    protected $models = null;    

    abstract protected function init();
    

    /**
     * @param $name
     * @return mixed
     */
    public function getModel($name)
    {
        if ($this->models === null) {
            $this->init();
        }
        
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildModel($name);
        }

        return $this->models[$name];
    }    

}