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
    
    protected $filename = null;
    

    public function register()
    {
        spl_autoload_register([$this, 'load']);
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
        $this->filename = null;
        if (isset($this->invalid[$class_name])) {
            return $this;
        }
        
        foreach ($this->resources as  $namespace => $path) {
            $this->filename = $path.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
            $included = $this->includeWhenExists($this->filename);
            if ($included) {
                return $this;
            }

            $this->filename = $path.trim(str_replace($namespace, '', $class_name), '\\').'.php';
            $this->filename = str_replace('\\', DIRECTORY_SEPARATOR, $this->filename);
            $included = $this->includeWhenExists($this->filename);
            if ($included) {
                return $this;
            }
            
            $this->invalid[$class_name] = true;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFilename()
    {
        return $this->filename;
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