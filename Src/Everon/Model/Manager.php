<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
            $this->models[$name] = $this->getFactory()->buildModel($name);
        }

        return $this->models[$name];
    }    

}