<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

use Everon\Dependency;
use Everon\Interfaces;

abstract class Manager implements Interfaces\DomainManager
{
    use Dependency\Injection\Factory;

    /**
     * List of Models
     * @var array
     */
    protected $models = null;
    
    abstract protected function init();
    
    
    public function __construct()
    {
        $this->init();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModel($name)
    {
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildDomainModel($name);
        }

        return $this->models[$name];
    }
    
    /**
     * @param $name
     * @return \Everon\Domain\Interfaces\Repository
     */
    public function getRepository($name)
    {
        if (isset($this->models[$name]) === false) {
            $this->models[$name] = $this->getFactory()->buildDomainRepository($name);
        }

        return $this->models[$name];
    }    

}