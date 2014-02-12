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
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;

class Manager implements Interfaces\ModuleManager
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;

    use Helper\Arrays;
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
    use Helper\IsIterable;

    /**
     * @var array
     */
    protected $modules = null;
    
    protected $configs_registered = false;
    
    
    protected function initConfigs()
    {
        if ($this->configs_registered) {
            return;
        }
        
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            $module_name = $Dir->getBasename();
            $this->registerModuleConfigs($module_name);
        }
        
        $this->configs_registered = true;
    }

    protected function registerModuleConfigs($module_name)
    {
        $filename = $this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/module.ini');
        $this->getConfigManager()->registerByFilename($module_name.'@'.'module', $filename);
        
        $filename = $this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/router.ini');
        $this->getConfigManager()->registerByFilename($module_name.'@'.'router', $filename);
    }

    protected function initModules()
    {
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            $module_name = $Dir->getBasename();

            if (isset($this->modules[$module_name])) {
                throw new Exception\Module('Module: "%s" is already registered');
            }
            
            $Config = $this->getModuleConfig($module_name, 'module');
            $ConfigRouter = $this->getModuleConfig($module_name, 'router');
            $this->modules[$module_name] = $this->getFactory()->buildModule($module_name, $Dir->getPathname(), $Config, $ConfigRouter);
        }
    }
    
    protected function getModuleConfig($module_name, $config_name)
    {
        $this->initConfigs();
        $name = $module_name.'@'.$config_name;
        return $this->getConfigManager()->getConfigByName($name);
    }

    public function getModule($name)
    {
        if ($this->modules === null) {
            $this->initModules();
        }
        
        return $this->modules[$name];
    }
    
    public function getModuleByRequest()
    {
        
    }

}