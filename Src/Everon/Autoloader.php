<?php
//todo: PSR-0 it
namespace Everon;

class Autoloader
{
    protected $class_map = [];

    protected $class_map_enabled = false;

    public function __construct($use_class_map)
    {
        $this->class_map_enabled = $use_class_map;
    }

    public function addToMap($class, $file)
    {
        if ($this->class_map_enabled === false) {
            return;
        }
        if (isset($this->class_map[$class]) === false) {
            $this->class_map[$class] = $file;
            $this->saveMap();
        }
    }

    public function getFilenameFromMap($class)
    {
        if ($this->class_map_enabled && isset($this->class_map[$class])) {
            return $this->class_map[$class];
        }

        return null;
    }

    public function getCacheFilename($tmp=ev_DIR_CACHE, $os=ev_OS)
    {
        return $tmp.'everon_classmap_'.$os.'.php';
    }

    public function loadMap()
    {
        if ($this->class_map_enabled === false) {
            return;
        }

        $filename = $this->getCacheFilename();
        if (is_file($filename)) {
            $this->class_map = include($filename);
        }
    }

    public function saveMap()
    {
        if ($this->class_map_enabled === false) {
            return;
        }

        $data = var_export($this->class_map, 1);
        $filename = $this->getCacheFilename();
        $h = fopen($filename, 'w+');
        fwrite($h, "<?php return $data; ");
        fclose($h);
    }

    public function autoload($class_name)
    {
        $filename = $this->getFilenameFromMap($class_name);
        if (is_null($filename) === false) {
            require_once($filename);
            return;
        }
        
        if ($this->isNamespace($class_name, 'everon') === false) {
            return;
        }        

        $path = $class_name;
        $php_file = ev_DIR_ROOT.$path.'.php'; //var/www/everon/everon\controller\bookcatalog.php

        //as it is
        if (is_file($php_file) === false) {
            $path = $this->stripNamespace($class_name);
            $php_file = ev_DIR_ROOT.$path.'.php';
        }

        if (is_file($php_file)) {
            require_once($php_file);
            self::addToMap($class_name, $php_file);
        }
        else { //load everon internal class
            $class_file = $this->getFilenameByClassName($class_name, ev_DIR_SRC);
            if (is_file($class_file)) {
                require_once($class_file);
                self::addToMap($class_name, $class_file);
            }
            else {
                throw new \RuntimeException(vsprintf(
                    'File for class: "%s" could not be found', $class_name
                ));
            }
        }
    }
    
    public function isNamespace($namespace)
    {
        return substr(strtolower($namespace),0,6) == 'everon';
    }
    
    public function stripNamespace($namespace)
    {
        return preg_replace('/^([\\\]?)everon([\\\]?)/i', '', $namespace);
    }
    
    public function getFilenameByClassName($class, $directory=ev_DIR_SRC)
    {
        $filename = $directory.$class.'.php';
        return $this->fixPath($filename);
    }

    public function fixPath($path)
    {
        return str_replace(array('/', '\\'), \ev_DS, $path);
    }    

}