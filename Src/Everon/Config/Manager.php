<?php
namespace Everon\Config;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;

class Manager implements Interfaces\ConfigManager
{
    use Dependency\Injection\Factory;
    
    use Helper\Asserts;
    use Helper\Asserts\IsArrayKey;


    /**
     * @var array
     */
    protected $configs = null;

    /**
     * @var boolean
     */
    protected $use_cache = null;

    protected $config_directory = null;

    protected $cache_directory = null;

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
     * @param $directory
     * @param $cache_directory
     */
    public function __construct(Interfaces\ConfigExpressionMatcher $Matcher, $directory, $cache_directory)
    {
        $this->ExpressionMatcher = $Matcher;
        $this->config_directory = $directory;
        $this->cache_directory = $cache_directory;
    }

    protected function loadAndRegisterConfigs()
    {
        $this->setupCachingAndDefaultConfig();
        
        $Compiler = $this->ExpressionMatcher->getCompiler($this);

        /**
         * @var \SplFileInfo $file
         */
        $IniFiles = new \GlobIterator($this->config_directory.'*.ini');
        foreach ($IniFiles as $config_filename => $file) {
            if (strcasecmp($file->getFilename(), $this->default_config_filename) === 0) {
                continue; //don't load default config again
            }

            $filename = $this->cache_directory.$file->getFilename().'.php';
            if ($this->use_cache && is_file($filename)) {
                $name = basename(basename($config_filename, '.php'), '.ini');
                $ini_config_data = function() use ($filename, $Compiler) {
                    $cache = null;
                    include($filename);
                    $Compiler($cache);
                    return $cache;
                };
            }
            else {
                $name = basename($config_filename, '.ini');
                $ini_config_data = function() use ($config_filename, $Compiler) {
                    $content = parse_ini_file($config_filename, true);
                    $Compiler($content);
                    return $content;
                };
            }
            
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
        $mergeDefaults = function($default, $data) use (&$mergeDefaults) {
            foreach ($default as $name => $value) {
                if (is_array($value)) {
                    $data[$name] = $mergeDefaults($default[$name], $data[$name]);
                }
                else {
                    if (isset($data[$name]) === false) {
                        $data[$name] = $default[$name];
                    }
                }
            }

            return $data;
        };

        $data = $this->default_config_data;

        $ini = @parse_ini_file($this->config_directory.$this->default_config_filename, true);
        if (is_array($ini)) {
            $data = $mergeDefaults($data, $ini);
        }

        $Config = $this->getFactory()->buildConfig(
            $this->default_config_name,
            $this->config_directory.$this->default_config_filename,
            $data
        );

        return $Config;
    }
    
    protected function setupCachingAndDefaultConfig()
    {
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
        $this->saveConfigToCache($Config);
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

    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    protected function saveConfigToCache(Interfaces\Config $Config)
    {
        if ($this->use_cache === false) {
            return;
        }
        
        try {
            $cache_filename = $this->cache_directory.pathinfo($Config->getFilename(), PATHINFO_BASENAME).'.php';
            
            if (!is_dir($this->cache_directory)) {
                mkdir($this->cache_directory, 0775, true);
            }

            $data = var_export($Config->toArray(), true);
            $h = fopen($cache_filename, 'w+');
            fwrite($h, "<?php \$cache = $data; ");
            fclose($h);
        }
        catch (\Exception $e) {
            throw new Exception\Config('Unable to save config cache file: "%s"', $Config->getFilename(), $e);
        }
    }

}