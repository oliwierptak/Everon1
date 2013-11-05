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

class Bootstrap
{
    /**
     * @var \Everon\Interfaces\Environment
     */
    protected $Environment = null;

    /**
     * @var \Everon\Interfaces\ClassLoader
     */
    protected $ClassLoader = null;
    
    protected $os_name = null;
    
    
    public function __construct($Environment, $os=PHP_OS)
    {
        $this->Environment = $Environment;
        $os = substr($os, 0, 3);
        $this->os_name = $os === 'WIN' ? 'win' : 'unix';
    }
    
    public function getClassLoader()
    {
        return $this->ClassLoader;
    }
    
    public function getEnvironment()
    {
        return $this->Environment;
    }
    
    public function getOsName()
    {
        return $this->os_name;
    }
    
    protected function setupClassLoader()
    {
        require_once($this->Environment->getEveronInterface().'ClassLoader.php');
        require_once($this->Environment->getEveron().'ClassLoader.php');

        $use_cache = false;
        $ini = @parse_ini_file($this->Environment->getConfig().'application.ini', true);
        if (is_array($ini) && array_key_exists('cache', $ini) && array_key_exists('autoloader', $ini['cache'])) {
            $use_cache = (bool) $ini['cache']['autoloader'];
        }

        $ClassMap = null;
        if ($use_cache) {
            require_once($this->Environment->getEveron().'ClassLoaderCache.php');
            require_once($this->Environment->getEveronInterface().'ClassMap.php');
            require_once($this->Environment->getEveron().'ClassMap.php');

            $classmap_filename = $this->Environment->getCache().'everon_classmap_'.$this->getOsName().'.php';
            $ClassMap = new ClassMap($classmap_filename);
            $ClassLoader = new ClassLoaderCache($ClassMap);
        }
        else {
            $ClassLoader = new ClassLoader();
        }
        
        $this->ClassLoader = $ClassLoader;
    }
    
    protected function registerClassLoader()
    {
        $this->setupClassLoader();
        
        $this->getClassLoader()->add('Everon', $this->getEnvironment()->getSource());
        $this->getClassLoader()->add('Everon\View', $this->getEnvironment()->getView());
        $this->getClassLoader()->register();
    }
    
    public function run()
    {
        $this->registerClassLoader();
        
        require_once($this->getEnvironment()->getEveron().'Exception.php');
        require_once($this->getEnvironment()->getEveron().'Exception/Repository.php');

        require_once($this->getEnvironment()->getEveronLib().'Dependencies.php');
        
        return [$Container, $Factory];
    }
}