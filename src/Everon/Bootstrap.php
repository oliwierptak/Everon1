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

class Bootstrap
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
    
    protected $application_ini = null;
    
    protected $environment_name = null;
    
    protected $show_auto_loader_exceptions = null;
    
    
    public function __construct($Environment, $environment_name, $os=PHP_OS)
    {
        $this->environment_name = trim($environment_name);
        if ($this->environment_name === '') {
            throw new \Exception('Undefined environment name');
        }
        
        $this->Environment = $Environment;
        $os = substr($os, 0, 3);
        $this->os_name = $os === 'WIN' ? 'win' : 'unix';
        
        //define config directory based on EVERON_ENVIRONMENT
        $ConfigDir = new \SplFileInfo($this->Environment->getConfig().$this->environment_name);
        if ($ConfigDir->isDir() === false) {
            throw new \Exception(sprintf('Invalid config directory: "%s"', $ConfigDir->getPathname()));
        }
                
        $this->Environment->setConfig($ConfigDir->getPathname().DIRECTORY_SEPARATOR);
    }

    /**
     * @param bool $show_auto_loader_exceptions
     */
    public function setShowAutoLoaderExceptions($show_auto_loader_exceptions)
    {
        $this->show_auto_loader_exceptions = $show_auto_loader_exceptions;
    }

    /**
     * @return bool
     */
    public function getShowAutoLoaderExceptions()
    {
        if ($this->show_auto_loader_exceptions === null) {
            $ini = $this->getApplicationIni();
            $this->show_auto_loader_exceptions = @$ini['autoloader']['throw_exceptions'] === true;
        }
        return $this->show_auto_loader_exceptions;
    }
    
    public function getClassLoader()
    {
        return $this->ClassLoader;
    }
    
    public function getEnvironment()
    {
        return $this->Environment;
    }
    
    public function setEnvironment(Environment $Environment)
    {
        return $this->Environment = $Environment;
    }
    
    public function getOsName()
    {
        return $this->os_name;
    }
    
    protected function setupClassLoader()
    {
        require_once($this->Environment->getEveronInterface().'ClassLoader.php');
        require_once($this->Environment->getEveronRoot().'ClassLoader.php');

        $ini = $this->getApplicationIni();
        $use_cache = (bool) @$ini['cache']['autoloader'];

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
    
    protected function getApplicationIni()
    {
        if ($this->application_ini === null) {
            $this->application_ini = @parse_ini_file($this->Environment->getConfig().'application.ini', true);
        }
        return $this->application_ini;
    }

    protected function registerClassLoader($prepend_autoloader)
    {
        if ($this->hasAutoloader('composer')) {
            require_once($this->Environment->getRoot().'vendor/autoload.php');
        }
        
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
    }

    public function hasAutoloader($name)
    {
        $ini = $this->getApplicationIni();
        $autoloaders = @$ini['autoloader']['active'];
        
        if (is_array($autoloaders)) {
            return in_array($name, $autoloaders);
        }
        
        return false;
    }

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

    public static function setupExceptionHandler($guid_value, $app_root, $log_filename)
    {
        $log_directory = implode(DIRECTORY_SEPARATOR, [$app_root, 'Tmp', 'logs']);
        $log_filename = $log_directory.DIRECTORY_SEPARATOR.$log_filename;

        set_exception_handler(function ($Exception) use ($guid_value, $log_filename) {
            Bootstrap::logException($Exception, $guid_value, $log_filename);
            
            if (php_sapi_name() !== 'cli' || headers_sent() === false) {
                http_response_code(500);
                header("EVRID: $guid_value");
            }
        });
    }
    
    public static function logException(\Exception $Exception, $guid_value, $log_filename)
    {
        $timestamp = date('c', time());
        $id = substr($guid_value, 0, 6);
        $message = "$timestamp ${id} \n$Exception \n\n";
        error_log($message, 3, $log_filename);
        return $message;
    }

    /**
     * @param string $environment_name
     */
    public function setEnvironmentName($environment_name)
    {
        $this->environment_name = $environment_name;
    }

    /**
     * @return string
     */
    public function getEnvironmentName()
    {
        return $this->environment_name;
    }
    
    
    
}