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

abstract class Container implements Interfaces\DependencyContainer
{
    protected $definitions = [];

    protected $services = [];

    protected $container_dependencies = [];

    protected $class_dependencies_to_inject = [];
    
    protected $wants_factory = [];
    
    protected $circular_dependencies = [];
    
    protected $excluded = [];
    
    //public static $TMP_COUNTER = [];
    

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
     * @param $container_name
     * @param $Receiver
     * @throws Exception\DependencyContainer
     */
    protected function dependencyToObject($dependency_name, $container_name, $Receiver)
    {
        $method = 'set'.$container_name; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new Exception\DependencyContainer(
                'Required dependency setter: "%s" is missing in: "%s"',
                [$dependency_name, $container_name]
            );
        }

        $Receiver->$method($this->resolve($container_name));
    }

    /**
     * @param $dependency_name
     * @return string
     * @throws \Everon\Exception\DependencyContainer
     */
    protected function getContainerNameFromDependencyToInject($dependency_name)
    {
        if (isset($this->container_dependencies[$dependency_name])) {
            return $this->container_dependencies[$dependency_name];
        }
        
        if (isset($this->excluded[$dependency_name])) {
            return '';
        }

        $tokens = explode('\Dependency\Injection', $dependency_name);
        if (count($tokens) <= 1) {
            $this->excluded[$dependency_name] = true;
            return '';
        }
        
        $container_name = ltrim(@$tokens[1], '\\'); //eg. from Everon\Dependency\Injection\FooManager into FooManager
        if ($container_name === '') {
            throw new Exception\DependencyContainer('Unresolved container name for: "%"', $dependency_name);
        }

        $this->container_dependencies[$dependency_name] = $container_name;
        
        return $container_name;
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     */
    protected function getClassDependencies($class, $autoload=true) 
    {
        //self::$TMP_COUNTER[$name] = @intval(self::$TMP_COUNTER[$name]) + 1;
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
     * @inheritdoc
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
                $result = $this->getContainerNameFromDependencyToInject($name) !== '';
                return $result;
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
                $container_name = $this->getContainerNameFromDependencyToInject($name);
                if (isset($this->circular_dependencies[$container_name])) {
                    if (in_array($class_name, $this->circular_dependencies[$container_name])) {
                        throw new Exception\DependencyContainer('Circular dependency injection: "%s" in class: "%s"', [$container_name, $class_name]);
                    }
                }
                $this->dependencyToObject($name, $container_name, $Receiver);
            }
        }

        return $Receiver;
    }

    /**
     * @inheritdoc
     */
    public function wantsFactory($class_name)
    {
        return isset($this->wants_factory[$class_name]) && $this->wants_factory[$class_name]; 
    }

    /**
     * @inheritdoc
     */
    public function register($name, \Closure $ServiceClosure)
    {
        $this->definitions[$name] = $ServiceClosure;
        unset($this->services[$name]);
    }

    /**
     * @inheritdoc
     */
    public function propose($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            return;
        }
        
        $this->register($name, $ServiceClosure);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isRegistered($name)
    {
        return (isset($this->definitions[$name]) || isset($this->services[$name]));
    }

    /**
     * @inheritdoc
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @inheritdoc
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }
    
}