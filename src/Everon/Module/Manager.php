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
    
    
    protected function initConfigs()
    {
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            $name = $Dir->getBasename();
            $this->registerModuleConfigs($name);
        }
    }

    protected function registerModuleConfigs($module_name)
    {
        $config_directory = $this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/');
        $default_data = $this->getConfigManager()->getConfigDataFromLoader($this->getConfigManager()->getConfigLoader());
        $ConfigLoader = $this->getFactory()->buildConfigLoader($config_directory, $this->getEnvironment()->getCacheConfig());

        //merge module config data with application configs data for expressions to be compiled
        $module_config_data = $this->getConfigManager()->getConfigDataFromLoader($ConfigLoader);
        foreach ($module_config_data as $name => $data) {
            $default_data[$module_name.'_'.$name] = $data;
        }

        //compile
        list($Compiler, $default_config_items_data) = $this->getConfigManager()->getAllConfigsDataAndCompiler($default_data);

        //register
        foreach ($module_config_data as $name => $ConfigLoaderItem) {
            $config_name = $module_name.'_'.$name;
            $data = $default_config_items_data[$config_name];
            $ConfigLoaderItem->setData($data);
            $this->getConfigManager()->loadAndRegisterOneConfig($config_name, $ConfigLoaderItem, $Compiler);
        }
    }

    protected function initModules()
    {
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            $name = $Dir->getBasename();

            if (isset($this->modules[$name])) {
                throw new Exception\Module('Module: "%s" is already registered');
            }
            
            $Config = $this->getModuleConfig($name, 'module');
            $ConfigRouter = $this->getModuleConfig($name, 'router');
            $this->modules[$name] = $this->getFactory()->buildModule($name, $Config, $ConfigRouter);
        }
    }
    
    protected function getModuleConfig($module_name, $config_name)
    {
        if ($this->modules === null) {
            $this->initConfigs();
        }
        
        $name = $module_name.'_'.$config_name;
        return $this->getConfigManager()->getConfigByName($name);
    }

    public function getModule($name)
    {
        if ($this->modules === null) {
            $this->initModules();
        }
        
        return $this->modules[$name];
    }
    
}