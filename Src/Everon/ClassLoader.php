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
    protected $class_map_enabled = false;
    
    protected $resources = [];

    /**
     * @var Interfaces\ClassMap|null
     */
    protected $ClassMap = null;
    

    /**
     * @param Interfaces\ClassMap|null $ClassMap
     */
    public function __construct($ClassMap)
    {
        if ($ClassMap instanceof \Everon\Interfaces\ClassMap) {
            $this->class_map_enabled = true;
            $this->ClassMap = $ClassMap;
            $this->ClassMap->loadMap(); 
        }
    }
   
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
     * @throws \RuntimeException
     */
    public function load($class_name)
    {
        if ($this->class_map_enabled) {
            $filename = $this->ClassMap->getFilenameFromMap($class_name);
            if ($filename !== null) {
                include_once($filename);
                return;
            }
        }
        
        $include = false;
        $filename = '';
        foreach ($this->resources as $namespace => $path) {
            $filename = $path.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
            $include = $this->includeWhenExists($filename);
            if ($include !== false) {
                break;
            }

            $filename = $path.trim(str_replace($namespace, '', $class_name), '\\').'.php';
            $include = $this->includeWhenExists($filename);
            if ($include !== false) {
                break;
            }
        }

        if ($include === false) {
            throw new \RuntimeException(vsprintf(
                'File for class: "%s" could not be found', $class_name
            ));
        }
        
        if ($this->class_map_enabled) {
            $this->ClassMap->addToMap($class_name, $filename);
        }        
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