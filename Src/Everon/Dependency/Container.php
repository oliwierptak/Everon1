<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;

use Everon\Exception;
use Everon\Interfaces;

class Container implements Interfaces\DependencyContainer
{
    protected $definitions = [];

    protected $services = [];

    protected $container_dependencies = [];

    protected $class_dependencies = [];
    
    protected $wants_factory = [];


    /**
     * @param $dependency_name
     * @param mixed $Receiver
     * @return null
     * @throws Exception\DependencyContainer
     */
    protected function dependencyToObject($dependency_name, $Receiver)
    {
        if (array_key_exists($dependency_name, $this->container_dependencies)) {
            $container_name = $this->container_dependencies[$dependency_name];
        }
        else {
            if ($container_name = $this->getContainerNameFromDependency($dependency_name)) {
                $this->container_dependencies[$dependency_name] = $container_name;
            }
        }

        $method = 'set'.$container_name; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new Exception\DependencyContainer(
                'Required dependency setter: "%s" is missing in: "%s"',
                [$container_name, $dependency_name]
            );
        }

        $Receiver->$method($this->resolve($container_name));
    }

    protected function getContainerNameFromDependency($dependency_name)
    {
        return substr($dependency_name, strlen('Everon\Dependency\Injection')+1);
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     */
    protected function getClassDependencies($class, $autoload = true) 
    {
        $traits = class_uses($class, $autoload);
        $parents = class_parents($class, $autoload);
        
        foreach ($parents as $parent) {
            $traits = array_merge(
                class_uses($parent, $autoload), 
                $traits
            );
        }

        return $traits;
    }

    /**
     * @param $class_name
     * @param $Receiver
     * @return mixed
     * @throws Exception\Factory
     */
    public function inject($class_name, $Receiver)
    {
        try {
            if (class_exists($class_name, true) === false) {
                throw new Exception\Factory('Dependency file could not be found for: "%s"', $class_name);
            }
        }
        catch (\Exception $e) {
            throw new Exception\Factory('Error injecting dependency: "%s"', $class_name);
        }

        if (array_key_exists($class_name, $this->class_dependencies) === false) {
            $OnlyInjections = function($name) {
                return substr($name, 0, strlen('Everon\Dependency\Injection'))  === 'Everon\Dependency\Injection';
            };
            
            $this->class_dependencies[$class_name] = array_filter(
                $this->getClassDependencies($class_name), 
                $OnlyInjections
            );
        }

        $dependencies = $this->class_dependencies[$class_name];
        foreach ($dependencies as $name) {
            if ($name === 'Everon\Dependency\Injection\Factory') {
                $this->wants_factory[$class_name] = true;
            }
            else {
                $this->dependencyToObject($name, $Receiver);
            }
        }

        return $Receiver;
    }
    
    public function wantsFactory($class_name)
    {
        return isset($this->wants_factory[$class_name]) && $this->wants_factory[$class_name]; 
    }

    /**
     * @param $name
     * @param \Closure $ServiceClosure
     */
    public function register($name, \Closure $ServiceClosure)
    {
        $this->definitions[$name] = $ServiceClosure;
        unset($this->services[$name]);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception\DependencyContainer
     */
    public function resolve($name)
    {
        if (isset($this->definitions[$name]) === false) {
            throw new Exception\DependencyContainer('Container does not contain: "%s"', $name);
        }

        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        $callback = $this->definitions[$name];
        if (is_callable($callback)) {
            $this->services[$name] = $callback();
        }

        return $this->services[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function isRegistered($name)
    {
        return (isset($this->definitions[$name]) || isset($this->services[$name]));
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }
    
}