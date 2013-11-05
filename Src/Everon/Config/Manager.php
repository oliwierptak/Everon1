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
    
    use Helper\Asserts\IsArrayKey;
    use Helper\ArrayMergeDefault;


    /**
     * @var array
     */
    protected $configs = null;

    protected $default_cache_config_filename = 'cache.ini';
    
    protected $default_config_name = 'cache';

    /**
     * @var array
     */
    protected $default_cache_config_data = [
        'cache' => [
            'config_manager' => false,
            'autoloader' => false,
        ]
    ];

    /**
     * @param Interfaces\ConfigLoader $Loader
     * @param Interfaces\ConfigExpressionMatcher $Matcher
     */
    public function __construct(Interfaces\ConfigLoader $Loader, Interfaces\ConfigExpressionMatcher $Matcher)
    {
        $this->ConfigLoader = $Loader;
        $this->ExpressionMatcher = $Matcher;
    }

    protected function loadAndRegisterConfigs()
    {
        $default_config_data = $this->getCacheConfigData();
        $configs_data = $this->getConfigLoader()->load((bool) $default_config_data['enabled']['config_manager']);
        $Compiler = $this->ExpressionMatcher->getCompiler($configs_data);

        foreach ($configs_data as $name => $ConfigLoaderItem) {
            if ($this->isRegistered($name) === false) {
                $Config = $this->getFactory()->buildConfig($name, $ConfigLoaderItem);
                $this->register($Config);
                $Config->setCompiler($Compiler);
            }
        }
    }

    protected function getCacheConfigData()
    {
        $data = $this->default_cache_config_data;
        $directory = $this->getConfigLoader()->getConfigDirectory();

        $ini = $this->getConfigLoader()->read($directory.$this->default_cache_config_filename);
        if (is_array($ini)) {
            $data = $this->arrayMergeDefault($data, $ini);
        }
        
        return $data;
    }
   
    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    public function register(Interfaces\Config $Config)
    {
        if (isset($this->configs[$Config->getName()])) {
            throw new Exception\Config('Config with name: "%s" already registered', $Config->getName());
        }
        
        $this->configs[$Config->getName()] = $Config;
        $this->getConfigLoader()->saveConfigToCache($Config);
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
     * @return \Everon\Interfaces\Config
     */
    public function getRouterConfig()
    {
        return $this->getConfigByName('router');
    }
    
    /**
     * @return \Everon\Interfaces\Config
     */
    public function getViewConfig()
    {
        return $this->getConfigByName('view');
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

}