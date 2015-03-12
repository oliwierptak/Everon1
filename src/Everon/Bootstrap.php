<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Interfaces\Environment;

class Bootstrap implements \Everon\Interfaces\Bootstrap
{
    /**
     * @var \Everon\Interfaces\Environment
     */
    protected $Environment = null;

    /**
     * @var \Everon\Interfaces\ClassLoader
     */
    protected $ClassLoader = null;
    
    protected $os_name = null;
    
    protected $everon_config_data = null;
    
    protected $environment_name = null;
    
    protected $show_auto_loader_exceptions = null;
    

    /**
     * @param $Environment
     * @param $environment_name
     * @param string $os
     * @throws \Exception
     */
    public function __construct($Environment, $environment_name, $os=PHP_OS)
    {
        $this->environment_name = trim($environment_name);
        if ($this->environment_name === '') {
            throw new \Exception('Undefined environment name');
        }
        
        $this->Environment = $Environment;
        $os = substr($os, 0, 3);
        $this->os_name = $os === 'WIN' ? 'win' : 'unix';
        
        $ConfigDir = new \SplFileInfo($this->Environment->getConfig());
        $ConfigFlavourDir = new \SplFileInfo($this->Environment->getConfig().$this->environment_name);
        
        if ($ConfigDir->isDir() === false) {
            throw new \Exception(sprintf('Invalid config directory: "%s"', $ConfigDir->getPathname()));
        }
                
        $this->Environment->setConfig($ConfigDir->getPathname().DIRECTORY_SEPARATOR);
        $this->Environment->setConfigFlavour($ConfigFlavourDir->getPathname().DIRECTORY_SEPARATOR);
    }

    /**
     * @inheritdoc
     */
    public function setShowAutoLoaderExceptions($show_auto_loader_exceptions)
    {
        $this->show_auto_loader_exceptions = (bool) $show_auto_loader_exceptions;
    }

    /**
     * @inheritdoc
     */
    public function getShowAutoLoaderExceptions()
    {
        if ($this->show_auto_loader_exceptions === null) {
            $ini = $this->getEveronIni();
            $this->show_auto_loader_exceptions = @$ini['autoloader']['throw_exceptions'] === true;
        }
        return $this->show_auto_loader_exceptions;
    }

    /**
     * @inheritdoc
     */
    public function getClassLoader()
    {
        return $this->ClassLoader;
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment()
    {
        return $this->Environment;
    }

    /**
     * @inheritdoc
     */
    public function setEnvironment(Environment $Environment)
    {
        return $this->Environment = $Environment;
    }

    /**
     * @inheritdoc
     */
    public function getOsName()
    {
        return $this->os_name;
    }
    
    protected function setupClassLoader()
    {
        require_once($this->Environment->getEveronInterface().'ClassLoader.php');
        require_once($this->Environment->getEveronRoot().'ClassLoader.php');

        $ini = $this->getEveronIni();
        $use_cache = (bool) @$ini['cache']['autoloader'];
        $extra_files = @$ini['autoloader']['files'] ?: [];
        $paths = @$ini['autoloader']['paths'] ?: null;
        
        foreach ($extra_files as $extra_class_name => $extra_filename) {
            if (is_file('../'.$extra_filename)) {
                require_once('../'.$extra_filename);
            }
        }

        if ($paths !== null) {
            array_push($paths, get_include_path());
            set_include_path(join(PATH_SEPARATOR, array_values($paths)));
        }
        
        $ClassMap = null;
        if ($use_cache) {
            require_once($this->Environment->getEveronRoot().'ClassLoaderCache.php');
            require_once($this->Environment->getEveronInterface().'ClassMap.php');
            require_once($this->Environment->getEveronRoot().'ClassMap.php');

            $classmap_filename = $this->Environment->getCache().'everon_classmap_'.$this->getOsName().'.php';
            $ClassMap = new ClassMap($classmap_filename);
            $ClassLoader = new ClassLoaderCache($this->getShowAutoLoaderExceptions(), $ClassMap);
        }
        else {
            $ClassLoader = new ClassLoader($this->getShowAutoLoaderExceptions());
        }
        
        $this->ClassLoader = $ClassLoader;
    }

    /**
     * @return array|null
     */
    protected function getEveronIni()
    {
        if ($this->everon_config_data === null) {
            $this->everon_config_data = $this->getIniData();
        }
        return $this->everon_config_data;
    }

    /**
     * @return array
     */
    protected function getIniData()
    {
        $config_data = @parse_ini_file($this->Environment->getConfigFlavour().'everon.ini', true);
        if ($config_data === false) {
            $config_data = @parse_ini_file($this->Environment->getConfig().'everon.ini', true);    
        }
        return $config_data;
    }

    protected function registerClassLoader($prepend_autoloader)
    {
        if ($this->hasAutoloader('everon')) {
            $this->setupClassLoader();
            $this->getClassLoader()->add('Everon', $this->getEnvironment()->getEveronRoot());
            $this->getClassLoader()->add('Everon\Application', $this->getEnvironment()->getApplication());
            $this->getClassLoader()->add('Everon\DataMapper', $this->getEnvironment()->getDataMapper());
            $this->getClassLoader()->add('Everon\Domain', $this->getEnvironment()->getDomain());
            $this->getClassLoader()->add('Everon\Module', $this->getEnvironment()->getModule());
            $this->getClassLoader()->add('Everon\Rest', $this->getEnvironment()->getRest());
            $this->getClassLoader()->add('Everon\View', $this->getEnvironment()->getView());
            $this->getClassLoader()->register($prepend_autoloader);
        }

        if ($this->hasAutoloader('composer')) {
            require_once($this->Environment->getRoot().'vendor/autoload.php');
        }
    }

    /**
     * @inheritdoc
     */
    public function hasAutoloader($name)
    {
        $ini = $this->getEveronIni();
        $autoloaders = @$ini['autoloader']['active'];
        
        if (is_array($autoloaders)) {
            return in_array($name, $autoloaders);
        }
        
        return false;
    }

    /**
     * @inheritdoc
     */
    public function run($prepend_autoloader=false)
    {
        $this->registerClassLoader($prepend_autoloader);
        
        require_once($this->getEnvironment()->getEveronHelper().'ToString.php');
        require_once($this->getEnvironment()->getEveronHelper().'Exceptions.php');
        require_once($this->getEnvironment()->getEveronRoot().'Exception.php');

        /**
         * @var Interfaces\DependencyContainer $Container
         * @var Interfaces\Factory $Factory
         */
        $Container = new Application\Dependency\Container();
        return new Application\Factory($Container);
    }

    /**
     * @inheritdoc
     */
    public static function setupExceptionHandler($guid_value, $app_root, $log_filename)
    {
        $log_directory = implode(DIRECTORY_SEPARATOR, [$app_root, 'Tmp', 'logs']);
        $log_filename = $log_directory.DIRECTORY_SEPARATOR.$log_filename;

        set_exception_handler(function ($Exception) use ($guid_value, $log_filename) {
            echo $Exception;
            Bootstrap::logException($Exception, $guid_value, $log_filename);
            
            if (php_sapi_name() !== 'cli' || headers_sent() === false) {
                http_response_code(500);
                header("EVRID: $guid_value");
            }
        });
    }

    /**
     * @inheritdoc
     */
    public static function logException(\Exception $Exception, $guid_value, $log_filename)
    {
        $timestamp = date('c', time());
        $id = substr($guid_value, 0, 6);
        $message = "$timestamp ${id} \n$Exception \n";
        $message .= $Exception->getTraceAsString()."\n\n";
        error_log($message, 3, $log_filename);
        return $message;
    }

    /**
     * @inheritdoc
     */
    public function setEnvironmentName($environment_name)
    {
        $this->environment_name = $environment_name;
    }

    /**
     * @inheritdoc
     */
    public function getEnvironmentName()
    {
        return $this->environment_name;
    }
}