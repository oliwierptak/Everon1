<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Interfaces;

class ClassLoader implements Interfaces\ClassLoader
{
    protected $resources = [];
    
    protected $invalid = [];
    
    protected $throw_exceptions = false;
    
    
    public function __construct($throw_exceptions)
    {
        $this->throw_exceptions = $throw_exceptions;
    }

    /**
     * @inheritdoc
     */
    public function register($prepend = false)
    {
        spl_autoload_register([$this, 'load'], true, $prepend);
    }
    
    public function unRegister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    /**
     * @param $namespace
     * @param $directory
     */
    public function add($namespace, $directory)
    {
        $this->resources[$namespace] = $directory;
    }

    /**
     * @param $class_name
     * @return Interfaces\ClassLoader
     * @throws \RuntimeException
     */
    public function load($class_name)
    {
        $filename = null;
        if (isset($this->invalid[$class_name])) {
            return null;
        }
        
        foreach ($this->resources as  $namespace => $path) {
            $filename = $path.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
            $included = $this->includeWhenExists($filename);
            if ($included) {
                return $filename;
            }

            $filename = $path.trim(str_replace($namespace, '', $class_name), '\\').'.php';
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, $filename);
            $included = $this->includeWhenExists($filename);
            if ($included) {
                return $filename;
            }
            
            $this->invalid[$class_name] = true;
        }
        
        if ($this->throw_exceptions) {
            throw new \RuntimeException(vsprintf(
                'File for class: "%s" could not be found', [$class_name]
            ));
        }

        return null;
    }

    /**
     * @param $filename
     * @return bool
     */
    protected function includeWhenExists($filename)
    {
        $exists = file_exists($filename);

        if ($exists) {
            include_once($filename);
            return true;
        }

        return false;
    }
}