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

    protected $class_dependencies_to_inject = [];
    
    protected $wants_factory = [];
    
    protected $circular_dependencies = [];
    

    /**
     * @inheritdoc
     */
    public function monitor($class, $dependencies)
    {
        $this->circular_dependencies[$class] = $dependencies;
    }

    /**
     * @inheritdoc
     */
    public function afterSetup(\Closure $Setup)
    {
        $Setup($this);
    }

    /**
     * @param $dependency_name
     * @param mixed $Receiver
     * @return null
     * @throws Exception\DependencyContainer
     */
    protected function dependencyToObject($dependency_name, $Receiver)
    {
        if (isset($this->container_dependencies[$dependency_name])) {
            $container_name = $this->container_dependencies[$dependency_name];
        }
        else {
            if ($container_name = $this->getContainerNameFromDependencyToInject($dependency_name)) {
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
    
    protected function demandsInjection($dependency_name)
    {
        $tokens = explode('\Dependency\Injection', $dependency_name);
        $container_name = ltrim(@$tokens[1], '\\');
        return $container_name !== '';
    }
    
    protected function getContainerNameFromDependencyToInject($dependency_name)
    {
        $tokens = explode('\Dependency\Injection', $dependency_name);
        $container_name = ltrim(@$tokens[1], '\\'); //eg. from Everon\Dependency\Injection\Environment\Test into Environment\Test
        
        if ($container_name === '') {
            throw new Exception\DependencyContainer('Unresolved container name for: "%"', $dependency_name);
        }
        
        return $container_name;
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
     * @throws \Everon\Exception\DependencyContainer
     */
    public function inject($class_name, $Receiver)
    {
        try {
            if (class_exists($class_name, true) === false) {
                throw new Exception\DependencyContainer('Dependency file could not be found for: "%s"', $class_name);
            }
        }
        catch (\Exception $e) {
            throw new Exception\DependencyContainer('Error injecting dependency: "%s"', $class_name);
        }

        if (isset($this->class_dependencies_to_inject[$class_name]) === false) {
            $OnlyInjections = function($name) {
                return $this->demandsInjection($name);
            };
            
            $this->class_dependencies_to_inject[$class_name] = array_filter(
                $this->getClassDependencies($class_name), 
                $OnlyInjections
            );
        }

        foreach ($this->class_dependencies_to_inject[$class_name] as $name) {
            if ($name === 'Everon\Dependency\Injection\Factory') {
                $this->wants_factory[$class_name] = true;
            }
            else {
                $dep_name = $this->getContainerNameFromDependencyToInject($name);
                if (isset($this->circular_dependencies[$dep_name])) {
                    if (in_array($class_name, $this->circular_dependencies[$dep_name])) {
                        throw new Exception\DependencyContainer('Circular dependency injection: "%s" in class: "%s"', [$dep_name, $class_name]);
                    }
                }
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
     * Register only if not already registered
     * 
     * @param $name
     * @param \Closure $ServiceClosure
     */
    public function propose($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            return;
        }
        
        $this->register($name, $ServiceClosure);
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
        
        if (is_callable($this->definitions[$name])) {
            $this->services[$name] = $this->definitions[$name]();
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