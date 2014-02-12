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
use Everon\Interfaces;

class Manager implements Interfaces\ConfigManager
{
    use Dependency\Injection\Environment;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;
    use Dependency\ConfigLoader;

    use Helper\Arrays;
    use Helper\Asserts;
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
    
    protected $use_cache = null;

    /**
     * @param Interfaces\ConfigLoader $Loader
     */
    public function __construct(Interfaces\ConfigLoader $Loader)
    {
        $this->ConfigLoader = $Loader;
    }
    
    public function isCachingEnabled()
    {
        if ($this->use_cache === null) {
            $default_config_data = $this->getDefaultConfigData();
            $this->use_cache = (bool) $default_config_data['cache']['config_manager'];
            if ($this->use_cache === null) {
                $this->use_cache = false;
            }
        }
        return $this->use_cache;
    }
    
    /**
     * @param Interfaces\ConfigLoader $Loader
     * @return array
     */
    public function getConfigDataFromLoader(Interfaces\ConfigLoader $Loader)
    {
        return $Loader->load((bool) $this->isCachingEnabled());
    }
    
    public function getAllConfigsDataAndCompiler($configs_data)
    {
        /**
         * @var Interfaces\ConfigLoaderItem $ConfigLoaderItem
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
    
    protected function loadAndRegister()
    {
        $configs_data = $this->getConfigDataFromLoader($this->getConfigLoader());
        list($Compiler, $config_items_data) = $this->getAllConfigsDataAndCompiler($configs_data);

        foreach ($configs_data as $name => $ConfigLoaderItem) {
            $ConfigLoaderItem->setData($config_items_data[$name]);
            $this->loadAndRegisterOneConfig($name, $ConfigLoaderItem, $Compiler);
        }
    }
    
    public function loadAndRegisterOneConfig($name, $ConfigLoaderItem, $Compiler) //xxx
    {
        if ($this->isRegistered($name) === false) {
            $Config = $this->getFactory()->buildConfig($name, $ConfigLoaderItem, $Compiler);
            $this->register($Config);
        }
    }
    
    public function registerByFilename($config_name, $filename)
    {
        $default_data = [];
        /**
         * @var Interfaces\Config $Config
         */
        foreach ($this->getConfigs() as $name => $Config) {
            $default_data[$name] = $Config->toArray();
        }

        $default_data[$config_name] = parse_ini_file($filename, true);
        $Compiler = $this->getExpressionMatcher()->getCompiler($default_data, $this->getEnvironmentExpressions());
        $Compiler($default_data);
        
        $data = $default_data[$config_name];
        $ConfigLoaderItem = $this->getFactory()->buildConfigLoaderItem($filename, $data);
        $ConfigLoaderItem->setData($data);
        $this->loadAndRegisterOneConfig($config_name, $ConfigLoaderItem, $Compiler);
    }

    /**
     * @return array
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

[database]
driver = mysql

[assets]
css = %application.env.url_statc%css/
images = %application.env.url_statc%images/
js = %application.env.url_statc%js/
themes = %application.env.url_statc%themes/

[cache]
config_manager = false
autoloader = false
view = false

[model]
manager = Everon

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
     * @return Interfaces\ConfigExpressionMatcher
     */
    public function getExpressionMatcher()
    {
        if ($this->ExpressionMatcher === null) {
            $this->ExpressionMatcher = $this->getFactory()->buildConfigExpressionMatcher();
        }
        
        return $this->ExpressionMatcher;
    }
   
    /**
     * @param Interfaces\Config $Config
     * @throws \Everon\Exception\Config
     */
    public function register(Interfaces\Config $Config)
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
     * @param $name
     */
    public function unRegister($name)
    {
        @$this->configs[$name] = null;
        unset($this->configs[$name]);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isRegistered($name)
    {
        return isset($this->configs[$name]);
    }

    /**
     * @param $name
     * @return Interfaces\Config
     */
    public function getConfigByName($name)
    {
        if (is_null($this->configs)) {
            $this->loadAndRegister();
        }

        $this->assertIsArrayKey($name, $this->configs, 'Invalid config name: %s', 'Everon\Exception\Config');
        return $this->configs[$name];
    }


    /**
     * @param $expression
     * @return mixed|null
     */
    public function getConfigValue($expression)
    {
        $tokens = explode('.', $expression);
        $token_count = count($tokens);
        if ($token_count < 2) {
            return null;
        }
        
        if (count($tokens) == 2) { //view.compilers or application.env.url
            array_push($tokens, null);  
        }
        
        list($name, $section, $item) = $tokens;
        $Config = $this->getConfigByName($name);
        if ($item !== null) {
            $Config->go($section);
            return $Config->get($item, null);
        }
        
        return $Config->get($section);
    }

    
    /**
     * @return array|null
     */
    public function getConfigs()
    {
        if (is_null($this->configs)) {
            $this->loadAndRegister();
        }
        
        return $this->configs;
    }

    /**
     * @return Interfaces\Config
     */
    public function getDatabaseConfig()
    {
        $driver = $this->getConfigValue('application.database.driver', 'mysql');
        return $this->getConfigByName('database_'.$driver);
    }

}