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

class Manager implements Interfaces\Manager
{
    use Dependency\Injection\ConfigManager;
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;

    /**
     * @var array
     */
    protected $modules = null;
    
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
            $ConfigRouter = $this->getModuleConfig($module_name, 'router');
            $Module = $this->getFactory()->buildModule($module_name, $Dir->getPathname().DIRECTORY_SEPARATOR, $Config, $ConfigRouter);
            
            //has worker? register it
            if ((new \SplFileInfo($Dir->getPathname().DIRECTORY_SEPARATOR.'FactoryWorker.php'))->isFile()) {
                $Worker = $this->getFactory()->buildFactoryWorker($module_name);
                $Module->setFactoryWorker($Worker);
            }
            
            $this->modules[$module_name] = $Module;
        }
    }

    /**
     * @param $module_name
     * @param $config_name
     * @return Interfaces\Config
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
        $module_list = $this->getFileSystem()->listPathDir($this->getEnvironment()->getModule());
        $active_modules = $this->getConfigManager()->getConfigValue('application.modules.active', ['_Core']);

        /**
         * @var \DirectoryIterator $Dir
         */
        $result = [];
        foreach ($module_list as $Dir) {
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
    public function getModule($name)
    {
        if ($this->modules === null) {
            $this->initModules();
        }
        
        return $this->modules[$name];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultModule()
    {
        $default_module = $this->getConfigManager()->getConfigValue('application.modules.default', '_Core');
        return $this->getModule($default_module);
    }
    
    /**
     * @inheritdoc
     */
    public function getCoreModule()
    {
        return $this->getModule('_Core');
    }
}