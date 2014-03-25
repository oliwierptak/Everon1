<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;

class Manager implements \Everon\Config\Interfaces\Manager
{
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    use Dependency\Logger;
    use Dependency\ConfigLoader;

    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsArrayKey;
    use Helper\IsIterable;


    /**
     * @var array
     */
    protected $configs = null;

    protected $default_config_filename = 'application.ini';
    
    protected $default_config_name = 'application';
    
    protected $ExpressionMatcher = null; 

    /**
     * @var array
     */
    protected $default_config_data = null;
    
    protected $is_caching_enabled = null;


    /**
     * @param Interfaces\Loader $Loader
     */
    public function __construct(Interfaces\Loader $Loader)
    {
        $this->ConfigLoader = $Loader;
    }

    /**
     * @inheritdoc
     */
    public function isCachingEnabled()
    {
        if ($this->is_caching_enabled === null) {
            $default_config_data = $this->getDefaultConfigData();
            $this->is_caching_enabled = (bool) $default_config_data['cache']['config_manager'];
            if ($this->is_caching_enabled === null) {
                $this->is_caching_enabled = false;
            }
        }
        return $this->is_caching_enabled;
    }

    /**
     * @param bool $caching_enabled
     */
    public function setIsCachingEnabled($caching_enabled)
    {
        $this->is_caching_enabled = $caching_enabled;
    }
    
    /**
     * @param Interfaces\Loader $Loader
     * @return array
     */
    protected function getConfigDataFromLoader(Interfaces\Loader $Loader)
    {
        //load configs from application
        $data = $Loader->load((bool) $this->isCachingEnabled());
        
        //load router.ini data from all modules
        $data['router'] = [];
        $data_router = $this->getRouterDataFromAllModules();
        foreach ($data_router as $module_name => $module_router_data) {
            list($filename, $loader_data) = $module_router_data;
            $loader_data = $this->arrayPrefixKey($module_name.'@', $loader_data);
            $data[$module_name.'@router'] = $this->getFactory()->buildConfigLoaderItem($filename, $loader_data);
            $data['router'] = $this->arrayMergeDefault($data['router'], $loader_data); 
        }
        $data['router'] = $this->getFactory()->buildConfigLoaderItem('//Config/router.ini', $data['router']);
        
        //load module.ini data from all modules
        $module_list = $this->getPathsOfActiveModules();
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            $module_name = $Dir->getBasename();
            if ($Dir->isDot()) {
                continue;
            }
            $Filename = new \SplFileInfo($this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/module.ini'));
            $data[$module_name.'@module'] = $this->getConfigLoader()->loadByFile($Filename, $this->isCachingEnabled());
        }
        
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getPathsOfActiveModules()
    {
        $module_list = $this->getFileSystem()->listPathDir($this->getEnvironment()->getModule());
        $active_modules = $this->getDefaultConfigData();
        $active_modules = $active_modules['modules']['active'];

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
     * @param array $configs_data
     * @return array
     */
    protected function getAllConfigsDataAndCompiler(array $configs_data)
    {
        /**
         * @var Interfaces\LoaderItem $ConfigLoaderItem
         */
        $config_items_data = [];
        foreach ($configs_data as $name => $ConfigLoaderItem) {
            $config_items_data[$name] = $ConfigLoaderItem->toArray();
        }

        //compile expressions in one go
        $Compiler = $this->getExpressionMatcher()->getCompiler($config_items_data, $this->getEnvironmentExpressions());
        $Compiler($config_items_data);

        return [$Compiler, $config_items_data];
    }

    protected function loadAndRegisterAllConfigs()
    {
        $configs_data = $this->getConfigDataFromLoader($this->getConfigLoader());
        list($Compiler, $config_items_data) = $this->getAllConfigsDataAndCompiler($configs_data);

        /**
         * @var Interfaces\LoaderItem $ConfigLoaderItem
         */
        foreach ($configs_data as $name => $ConfigLoaderItem) {
            $ConfigLoaderItem->setData($config_items_data[$name]);
            $this->loadAndRegisterOneConfig($name, $ConfigLoaderItem, $Compiler);
        }
    }

    public function getRouterDataFromAllModules()
    {
        //gather router data from modules xxx
        $router_config_data = [];
        $module_list = $this->getPathsOfActiveModules();
        /**
         * @var \DirectoryIterator $Dir
         */
        foreach ($module_list as $Dir) {
            if ($Dir->isDot()) {
                continue;
            }
            
            $module_name = $Dir->getBasename();
            $RouterFilename = new \SplFileInfo($this->getFileSystem()->getRealPath('//Module/'.$module_name.'/Config/router.ini'));
            $RouterFilename = $this->getConfigLoader()->loadByFile($RouterFilename, $this->isCachingEnabled());
            $module_config_data = $RouterFilename->getData();
            
            foreach ($module_config_data as $section => $data) {
                $module_config_data[$section][Item\Router::PROPERTY_MODULE] = $module_name;
            }
            $router_config_data[$module_name] = [$RouterFilename->getFilename(), $module_config_data];
        }
        
        return $router_config_data;
    }

    /**
     * @param $name
     * @param $ConfigLoaderItem
     * @param $Compiler
     */
    protected function loadAndRegisterOneConfig($name, $ConfigLoaderItem, $Compiler)
    {
        if ($this->isRegistered($name) === false) {
            $Config = $this->getFactory()->buildConfig($name, $ConfigLoaderItem, $Compiler);
            $this->register($Config);
        }
    }

    /**
     * @return array
     */
    protected function getDefaultConfigData()
    {
        if ($this->default_config_data !== null) {
            return $this->default_config_data;
        }

        $this->default_config_data = parse_ini_string($this->getDefaults(), true);
        
        $directory = $this->getConfigLoader()->getConfigDirectory();
        $ini = $this->getConfigLoader()->read($directory.$this->default_config_filename);
        if (is_array($ini)) {
            $this->default_config_data = $this->arrayMergeDefault($this->default_config_data, $ini);
        }
        
        return $this->default_config_data;
    }
    
    protected function getDefaults()
    {
        return <<<EOF
; Everon application configuration example

[env]
url = /
url_statc = /assets/
name = everon-dev
autoload = everon       ;external, everon

[cache]
config_manager = false
autoloader = false
view = false

[modules]
default = Test
active[] = Test

[theme]
default = Main

[database]
driver = pgsql

[view]
compilers[e] = '.htm'
default_extension = '.htm'

[logger]
enabled = true
rotate = 512             ; KB
format = 'c'             ; todo: implment me
format[trace] = 'U'      ; todo: implment me
EOF;
    }

    /**
     * @inheritdoc
     */
    public function registerByFilename($config_name, $filename)
    {
        $default_data = [];
        $config_data = $this->getConfigs();
        /**
         * @var Interfaces\Config $Config
         */
        foreach ($config_data as $name => $Config) {
            $default_data[$name] = $Config->toArray();
        }

        $Filename = new \SplFileInfo($filename);
        $Filename = $this->getConfigLoader()->loadByFile($Filename, $this->isCachingEnabled());
        $default_data[$config_name] = $Filename->getData();
        $Compiler = $this->getExpressionMatcher()->getCompiler($default_data, $this->getEnvironmentExpressions());
        $Compiler($default_data);

        $data = $default_data[$config_name];
        $ConfigLoaderItem = $this->getFactory()->buildConfigLoaderItem($filename, $data);
        $ConfigLoaderItem->setData($data);
        $this->loadAndRegisterOneConfig($config_name, $ConfigLoaderItem, $Compiler);
    }

    /**
     * @inheritdoc
     */
    public function getEnvironmentExpressions()
    {
        $data = $this->getEnvironment()->toArray();
        foreach ($data as $key => $value) {
            $data["%environment.paths.$key%"] = $value;
            unset($data[$key]);
        }

        return $data;
    }
    
    /**
     * @inheritdoc
     */
    public function getExpressionMatcher()
    {
        if ($this->ExpressionMatcher === null) {
            $this->ExpressionMatcher = $this->getFactory()->buildConfigExpressionMatcher();
        }
        
        return $this->ExpressionMatcher;
    }
   
    /**
     * @inheritdoc
     */
    public function register(\Everon\Interfaces\Config $Config)
    {
        if (isset($this->configs[$Config->getName()])) {
            throw new Exception\Config('Config with name: "%s" already registered', $Config->getName());
        }

        $this->configs[$Config->getName()] = $Config;
        if ($this->isCachingEnabled()) {
            $this->getConfigLoader()->saveConfigToCache($Config);
        }
    }

    /**
     * @inheritdoc
     */
    public function unRegister($name)
    {
        @$this->configs[$name] = null;
        unset($this->configs[$name]);
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($name)
    {
        return isset($this->configs[$name]);
    }

    /**
     * @inheritdoc
     */
    public function getConfigByName($name)
    {
        if (is_null($this->configs)) {
            $this->loadAndRegisterAllConfigs();
        }

        $this->assertIsArrayKey($name, $this->configs, 'Invalid config name: %s', 'Config');
        return $this->configs[$name];
    }

    /**
     * @inheritdoc
     */
    public function setConfigByName(\Everon\Interfaces\Config $Config)
    {
        $this->configs[$Config->getName()] = $Config;
    }

    /**
     * @inheritdoc
     */
    public function getConfigValue($expression, $default=null)
    {
        try {
            $tokens = explode('.', $expression);
            $token_count = count($tokens);
            if ($token_count < 2) {
                return null;
            }

            if (count($tokens) == 2) { //application.env.url
                array_push($tokens, null);
            }

            list($name, $section, $item) = $tokens;
            $Config = $this->getConfigByName($name);
            if ($item !== null) {
                $Config->go($section);
                return $Config->get($item, $default);
            }

            return $Config->get($section, $default);
        }
        catch (Exception\Config $e) {
            $this->getLogger()->error($e);
            return $default;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getConfigs()
    {
        if (is_null($this->configs)) {
            $this->loadAndRegisterAllConfigs();
        }
        
        return $this->configs;
    }

    /**
     * @inheritdoc
     */
    public function getDatabaseConfig()
    {
        return $this->getConfigByName('database');
    }

}