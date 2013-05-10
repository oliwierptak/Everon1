<?php
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
        foreach ($this->resources as $namespace => $path) {
            if ($this->isNamespace($class_name, $namespace) === false) {
                continue;
            }

            $include = $this->includeFile($class_name, $namespace, $path);
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
            $this->ClassMap->addToMap($include[0], $include[1]);
        }        
    }

    /**
     * @param $class_name
     * @param $namespace
     * @param $path
     * @return array|bool
     */
    protected function includeFile($class_name, $namespace, $path)
    {
        $namespace = rtrim($namespace, '\\');
        $namespace = ltrim($namespace, '\\');
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        $filename = $this->includeWhenExists($class_name, $path);
        if ($filename !== false) {
            return [$class_name, $filename];
        }
        
        $class = substr($class_name, strlen($namespace), strlen($class_name));
        $class = ltrim($class, '\\');
        $filename = $this->includeWhenExists($class, $path);
        if ($filename !== false) {
            return [$class_name, $filename];
        }
        
        return false;
    }

    /**
     * @param $class
     * @param $path
     * @return string
     */
    protected function classToFileName($class, $path)
    {
        return $path.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    }

    /**
     * @param $class
     * @param $path
     * @return bool|string
     */
    protected function includeWhenExists($class, $path)
    {
        $filename = $this->classToFileName($class, $path);
        $exists = file_exists($filename);

        if ($exists) {
            include_once($filename);
            return $filename;
        }
        
        return false;
    }

    /**
     * @param $namespace
     * @param $match
     * @return bool
     */
    protected function isNamespace($namespace, $match)
    {
        $namespace = substr($namespace, 0, strlen($match));
        return strcasecmp($namespace, $match) == 0;
    }

}