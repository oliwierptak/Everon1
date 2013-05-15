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
    use Dependency\Injection\Factory;
    use Dependency\ConfigLoader;
    
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;
    use Helper\ArrayMergeDefault;


    /**
     * @var array
     */
    protected $configs = null;

    /**
     * @var boolean
     */
    protected $use_cache = null;

    protected $default_config_filename = 'application.ini';
    
    protected $default_config_name = 'application';

    /**
     * @var array
     */
    protected $default_config_data = [
        'url' => '/',
        'cache' => [
            'config_manager' => false,
            'autoloader' => false,
        ],
        'template' => [
            'compilers' => ['Curly']
        ]
    ];    

    /**
     * @var Interfaces\ConfigExpressionMatcher
     */
    protected $ExpressionMatcher = null;


    /**
     * @param Interfaces\ConfigExpressionMatcher $Matcher
     * @param Interfaces\ConfigLoader $Loader
     */
    public function __construct(Interfaces\ConfigExpressionMatcher $Matcher, Interfaces\ConfigLoader $Loader)
    {
        $this->ExpressionMatcher = $Matcher;
        $this->ConfigLoader = $Loader;
    }

    protected function loadAndRegisterConfigs()
    {
        $this->setupCachingAndDefaultConfig();
        
        $Compiler = $this->ExpressionMatcher->getCompiler($this);
        $list = $this->getConfigLoader()->getList($Compiler, $this->use_cache, $this->default_config_filename);
        
        foreach ($list as $name => $item) {
            list($config_filename, $ini_config_data) = $item;
            if ($this->isRegistered($name) === false) {
                $Config = $this->getFactory()->buildConfig($name, $config_filename, $ini_config_data);
                $this->register($Config);
            }
        }
    }

    /**
     * @return Interfaces\Config
     */
    protected function getDefaultConfig()
    {
        $data = $this->default_config_data;
        $directory = $this->getConfigLoader()->getConfigDirectory();
        
        $ini = $this->getConfigLoader()->read($directory.$this->default_config_filename);
        if (is_array($ini)) {
            $data = $this->arrayMergeDefault($data, $ini);
        }

        $Config = $this->getFactory()->buildConfig(
            $this->default_config_name,
            $directory.$this->default_config_filename,
            $data
        );

        return $Config;
    }
    
    protected function setupCachingAndDefaultConfig()
    {
        /**
         * @var Interfaces\Config $Config
         */
        if (isset($this->configs[$this->default_config_name]) === false) {
            $Config = $this->getDefaultConfig();
            $this->use_cache = (bool) $Config->go('cache')->get('config_manager');
            $this->register($Config);
        }
        else {
            $Config = $this->configs[$this->default_config_name];
            $this->use_cache = (bool) $Config->go('cache')->get('config_manager');
        }
    }

    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    public function register(Interfaces\Config $Config)
    {
        if (is_array($this->configs)) {
            if (array_key_exists($Config->getName(), $this->configs)) {
                throw new Exception\Config('Config with name: "%s" already registered', $Config->getName());
            }
        }
        
        $this->configs[$Config->getName()] = $Config;
        
        if ($this->use_cache) {
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
        return array_key_exists($name, $this->configs);
    }

    /**
     * @param $name
     * @return Interfaces\Config
     */
    public function getConfigByName($name)
    {
        if (is_null($this->configs)) {
            $this->loadAndRegisterConfigs();
        }

        $this->assertIsArrayKey($name, $this->configs, 'Invalid config name: %s', 'Config');
        return $this->configs[$name];
    }

    /**
     * @return \Everon\Interfaces\Config
     */
    public function getApplicationConfig()
    {
        return $this->getConfigByName('application');
    }

    /**
     * @return \Everon\Interfaces\ConfigRouter
     */
    public function getRouterConfig()
    {
        return $this->getConfigByName('router');
    }

    /**
     * @return array|null
     */
    public function getConfigs()
    {
        if (is_null($this->configs)) {
            $this->loadAndRegisterConfigs();
        }
        
        return $this->configs;
    }

    public function enableCache()
    {
        $this->use_cache = true;
    }

    public function disableCache()
    {
        $this->use_cache = false;
    }



}