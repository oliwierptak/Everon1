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

class Manager implements Interfaces\Manager
{
    use Dependency\Injection\Bootstrap;
    use Dependency\Injection\Factory;
    use Dependency\Injection\FileSystem;

    use Dependency\Logger;

    use Helper\Arrays;
    use Helper\Exceptions;
    use Helper\Asserts\IsArrayKey;
    use Helper\IsIterable;

    /**
     * @var \Everon\Config\Interfaces\Loader
     */
    protected $ConfigLoader = null;


    /**
     * @var \Everon\FileSystem\Interfaces\CacheLoader
     */
    protected $ConfigCacheLoader = null;

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
    
    protected $inheritance_symbol = '<';


    /**
     * @param Interfaces\Loader $Loader
     * @param \Everon\FileSystem\Interfaces\CacheLoader $ConfigCacheLoader
     */
    public function __construct(Interfaces\Loader $Loader,  \Everon\FileSystem\Interfaces\CacheLoader $ConfigCacheLoader)
    {
        $this->ConfigLoader = $Loader;
        $this->ConfigCacheLoader = $ConfigCacheLoader;
    }

    /**
     * @return \Everon\Config\Interfaces\Loader
     */
    public function getConfigLoader()
    {
        return $this->ConfigLoader;
    }

    /**
     * @param \Everon\Config\Interfaces\Loader $ConfigLoader
     */
    public function setConfigLoader(\Everon\Config\Interfaces\Loader $ConfigLoader)
    {
        $this->ConfigLoader = $ConfigLoader;
    }

    /**
     * @return \Everon\FileSystem\Interfaces\CacheLoader
     */
    public function getConfigCacheLoader()
    {
        return $this->ConfigCacheLoader;
    }

    /**
     * @param \Everon\FileSystem\Interfaces\CacheLoader $ConfigCacheLoader
     */
    public function setConfigCacheLoader(\Everon\FileSystem\Interfaces\CacheLoader $ConfigCacheLoader)
    {
        $this->ConfigCacheLoader = $ConfigCacheLoader;
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
        $ini = $this->getConfigLoader()->readIni($directory.$this->default_config_filename);
        if (is_array($ini)) {
            $this->default_config_data = $this->arrayMergeDefault($this->default_config_data, $ini);
        }

        return $this->default_config_data;
    }

    protected function getDefaults()
    {
        return <<<EOF
; Everon application configuration example

[locale]
database_timezone = UTC

[autoloader]
active[] = everon
active[] = composer
; files['Kint'] = vendor/raveren/kint/Kint.class.php
; paths['Mockery'] = vendor/mockery/mockery/library/
throw_exceptions = true

[cache]
config_manager = false
autoloader = false
view = false
datamapper = false

[module]
active[] = Foo

[view]
compilers[php] = '.php'
default_extension = '.php'
default_view = Index

[error_handler]
module = Rest
controller = Error
view = Error
validation_error_template = formSubmitOnError

[logger]
enabled = true
rotate = 512             ; KB
format = 'c'             ; todo: implment me
format[trace] = 'U'      ; todo: implment me

[server]
protocol = http://
host = everon.localhost
port_delim =
port =
url = /
location = %application.server.protocol%%application.server.host%%application.server.port_delim%%application.server.port%%application.server.url%
EOF;
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
        $data = $Loader->load();

        //load domain.ini
        $data['domain'] = $this->getConfigLoader()->loadFromFile(
            new \SplFileInfo($this->getBootstrap()->getEnvironment()->getDomainConfig().'domain.ini')
        );
        
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
            $data[$module_name.'@module'] = $this->getConfigLoader()->loadFromFile($Filename);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getPathsOfActiveModules()
    {
        $module_list = $this->getFileSystem()->listPathDir('//Module');
        $active_modules = $this->getDefaultConfigData();
        $active_modules = $active_modules['module']['active'];

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
        $config_items_data = [];
        foreach ($configs_data as $config_name => $config_loader_item) {
            $HasInheritance = function($value) {
                return strpos($value, $this->inheritance_symbol) !== false;
            };

            $inheritance_list = [];
            $data_processed = [];
            foreach ($config_loader_item['data'] as $section_name => $section_items) {
                if (strcasecmp($config_name, 'router') === 0) {
                    if (isset($section_items['url'])) {
                        $section_items['url'] = '%application.server.url%'.$section_items['url']; //auto append application url
                    }
                }
                
                if ($HasInheritance($section_name) === true) {
                    list($for, $from) = explode($this->inheritance_symbol, $section_name);
                    $for = trim($for);
                    $from = trim($from);
                    $inheritance_list[$for] = $from;
                    $data_processed[$for] = $section_items;
                }
                else {
                    $data_processed[$section_name] = $section_items;
                }

                if (empty($inheritance_list) === false) {
                    foreach ($inheritance_list as $for => $from) {
                        $this->assertIsArrayKey($for, $data_processed, 'Undefined config for section: "%s"');
                        $this->assertIsArrayKey($from, $data_processed, 'Undefined config from section: "%s"');
                        //$data_processed[$for] = $this->arrayMergeDefault($data_processed[$from], $data_processed[$for]);
                        $data_processed[$for] = array_merge($data_processed[$from], $data_processed[$for]);
                    }
                }
            }

            $config_items_data[$config_name] = [
                'filename' => $config_loader_item['filename'],
                'data' => $data_processed
            ];            
        }
        
        //compile expressions in one go
        $this->getExpressionMatcher()->compile($config_items_data, $this->getEnvironmentExpressions());
        return $config_items_data;
    }

    protected function loadAndRegisterAllConfigs()
    {
        $config_items_data = null;
        if ($this->isCachingEnabled()) {
            if ($this->getConfigCacheLoader()->cacheFileExists('config_manager')) {
                $CacheFile = $this->getConfigCacheLoader()->generateCacheFileByName('config_manager');
                $config_items_data = $this->getConfigCacheLoader()->loadFromCache($CacheFile);
            }
        }
        
        if ($config_items_data === null) {
            $configs_data = $this->getConfigDataFromLoader($this->getConfigLoader());
            $config_items_data = $this->getAllConfigsDataAndCompiler($configs_data);

            if ($this->getConfigCacheLoader()->cacheFileExists('config_manager') === false) {
                $this->getConfigCacheLoader()->saveToCache('config_manager', $config_items_data);
            }
        }
        
        foreach ($config_items_data as $config_name => $config_data) {
            $this->loadAndRegisterOneConfig($config_name, $config_data['filename'], $config_data['data']);
        }
    }

    /**
     * @param $name
     * @param $filename
     * @param $data
     */
    protected function loadAndRegisterOneConfig($name, $filename, $data)
    {
        if ($this->isRegistered($name) === false) {
            $Config = $this->getFactory()->buildConfig($name, $filename, $data);
            $this->register($Config);
        }
    }

    /**
     * @inheritdoc
     */
    /*    public function registerByFilename($config_name, $filename)
        {
            $default_data = [];
            $config_data = $this->getConfigs();
            / **
             * @var Interfaces\Config $Config
             * /
            foreach ($config_data as $name => $Config) {
                $default_data[$name] = $Config->toArray();
            }
    
            $Loader = $this->getConfigLoader()->loadFromFile(new \SplFileInfo($filename));
            $default_data[$config_name] = $Loader->getData();
            $Compiler = $this->getExpressionMatcher()->getCompiler($default_data, $this->getEnvironmentExpressions());
            $Compiler($default_data);
            
            $this->loadAndRegisterOneConfig($config_name, $Loader, $Compiler);
        }*/

    /**
     * @inheritdoc
     */
    public function getEnvironmentExpressions()
    {
        $data = $this->getBootstrap()->getEnvironment()->toArray();
        
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
     * @param $name
     * @return bool
     */
    public function hasConfig($name)
    {
        return isset($this->configs[$name]);
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

            if (count($tokens) == 2) { //application.env
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