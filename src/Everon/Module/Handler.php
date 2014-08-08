<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module;

use Everon\Dependency;
use Everon\Interfaces\Config;
use Everon\Exception;
use Everon\Helper;

abstract class Handler implements Interfaces\Handler
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;

    /**
     * @var array
     */
    protected $modules = null;

    /**
     * @var array
     */
    protected $factory_workers = null;
    
    protected $configs_were_registered = false;
    
    
    protected function initModules()
    {
        $path_list = $this->getPathsOfActiveModules();
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($path_list as $Dir) {
            $module_name = $Dir->getBasename();
            if (isset($this->modules[$module_name])) {
                throw new Exception\Module('Module: "%s" is already registered');
            }
            
            $Config = $this->getModuleConfig($module_name, 'module');
            $Module = $this->getFactory()->buildModule($module_name, $Dir->getPathname().DIRECTORY_SEPARATOR, $Config);           

            $Worker = $this->getFactoryWorker($module_name);
            $Module->setFactoryWorker($Worker);
            
            $Module->setup();
            $this->modules[$module_name] = $Module;
        }
    }

    /**
     * @param $module_name
     * @param $config_name
     * @return Config
     */
    protected function getModuleConfig($module_name, $config_name)
    {
        $name = $module_name.'@'.$config_name;
        return $this->getConfigManager()->getConfigByName($name);
    }

    /**
     * @inheritdoc
     */
    public function getPathsOfActiveModules()
    {
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        $active_modules = $this->getConfigManager()->getConfigValue('application.module.active', []);

        /**
         * @var \DirectoryIterator $Dir
         */
        $result = [];
        foreach ($module_list as $Dir) {
            if ($Dir->isDot()) {
                continue;
            }
            
            $module_name = $Dir->getBasename();
            if (in_array($module_name, $active_modules)) {
                $result[$module_name] = $Dir;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getModuleByName($name)
    {
        if ($this->modules === null) {
            $this->initModules();
        }
        if (isset($this->modules[$name]) === false) {
            throw new Exception\Module('Module: "%s" not found', $name);
        }
        
        return $this->modules[$name];
    }

    /**
     * @inheritdoc
     */
    public function setModuleByName($name, Interfaces\Module $Module)
    {
        $this->modules[$name] = $Module;
    }
    
    /**
     * @inheritdoc
     */
    public function getFactoryWorker($module_name)
    {
        if (isset($this->factory_workers[$module_name])) {
            return $this->factory_workers[$module_name];
        }
        
        $Dir = new \SplFileInfo($this->getFileSystem()->getRealPath('//Module/'.$module_name));
        if ((new \SplFileInfo($Dir->getPathname().DIRECTORY_SEPARATOR.'FactoryWorker.php'))->isFile()) {
            $Worker = $this->getFactory()->buildFactoryWorker($module_name);
        }
        else {
            $Worker = $this->getFactory()->buildFactoryWorker('Worker', 'Everon\Factory');
        }
        
        $this->factory_workers[$module_name] = $Worker;
        
        return $Worker;
    }

    /**
     * @inheritdoc
     */
    public function loadModuleDependencies()
    {
        $path_list = $this->getPathsOfActiveModules();
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($path_list as $Dir) {
            $module_name = $Dir->getBasename();
            //has custom dependency loaders? load them
            $loader_dir = new \SplFileInfo($Dir->getPathname().DIRECTORY_SEPARATOR.'Dependency'.DIRECTORY_SEPARATOR.'Loader');
            $LoaderFiles = new \GlobIterator($loader_dir.DIRECTORY_SEPARATOR.'*.php');
            /**
             * @var \SplFileInfo $File
             */
            foreach ($LoaderFiles as $filename => $File) {
                $Container = $this->getFactory()->getDependencyContainer();
                $FactoryWorker = $this->getFactoryWorker($module_name);
                include($File->getPathname());
            }
        }
    }
}