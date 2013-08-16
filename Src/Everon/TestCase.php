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

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    
    protected $suite_name = 'Everon'; //todo: this should come from PHPUnit enviroment

    /**
     * @var Interfaces\Environment
     */
    protected $Environment = null;

    /**
     * @var \Everon\Interfaces\Factory
     */
    protected $Factory = null;


    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->Environment = new Environment(PROJECT_ROOT);

        $dir = $this->getDoublesDirectory();
        $includes = [
            'MyController.php',
            'MyModelManager.php',
            'MyModel.php',
            'MyView.php'
        ];
        
        for ($a=0; $a<count($includes); ++$a) {
            include_once($dir.$includes[$a]);
        }
    }

    private function removeDirectoryRecursive($dir)
    {
        die('nononono');
        return;
        if (is_dir($dir)) {
            $It = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            array_map('unlink', iterator_to_array($It));
            rmdir($dir);
        }
    }
    
    public function getServerDataForRequest($data)
    {
        $server = [
            'SERVER_PROTOCOL'=> 'HTTP/1.1',
            'REQUEST_METHOD'=> $data['REQUEST_METHOD'],
            'REQUEST_URI'=> $data['REQUEST_URI'],
            'QUERY_STRING'=> $data['QUERY_STRING'],
            'SERVER_NAME'=> 'everon.nova',
            'SERVER_PORT'=> 80,
            'SERVER_ADDR'=> '127.0.0.1',
            'REMOTE_ADDR'=> '127.0.0.1',
            'HTTPS'=> 'off',
        ];

        return $server;
    }

    public function getTempDirectory()
    {
        return $this->Environment->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
    }

    public function getFixtureDirectory()
    {
        return $this->Environment->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR;
    }

    public function getStubDirectory()
    {
        return $this->Environment->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR;
    }

    public function getMockDirectory()
    {
        return $this->Environment->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'mocks'.DIRECTORY_SEPARATOR;
    }

    public function getDoublesDirectory()
    {
        return $this->Environment->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'doubles'.DIRECTORY_SEPARATOR;
    }

    public function getLogDirectory()
    {
        return $this->getTempDirectory().'logs'.DIRECTORY_SEPARATOR;
    }

    public function getConfigDirectory()
    {
        return $this->getFixtureDirectory().'config'.DIRECTORY_SEPARATOR;
    }
    
    public function getTemplateDirectory()
    {
        return $this->getFixtureDirectory().'templates'.DIRECTORY_SEPARATOR;
    }

    protected function getConfigCacheDirectory()
    {
        return $this->getTempDirectory().'config'.DIRECTORY_SEPARATOR;
    }
    
    public function getProtectedProperty($class_name, $name)
    {
        $Reflection = new \ReflectionClass($class_name);
        $Property = $Reflection->getProperty($name);
        $Property->setAccessible(true);
        
        return $Property;
    }
    
    public function getProtectedMethod($class_name, $name)
    {
        $Reflection = new \ReflectionClass($class_name);
        $Method = $Reflection->getMethod($name);
        $Method->setAccessible(true);
        
        return $Method;
    }
    
    public function getFactory()
    {
        if ($this->Factory !== null) {
            return $this->Factory;
        }

        if ($this->Factory === null) {
            $this->Factory = new Factory(new Dependency\Container());
        }

        $Container = $this->Factory->getDependencyContainer();
        $Factory = $this->Factory;
        $Environment = $this->Environment;
        
        $Environment->setLog($this->getLogDirectory());
        $Environment->setConfig($this->getConfigDirectory());
        $Environment->setCacheConfig($this->getConfigCacheDirectory());
        $Environment->setViewTemplate($this->getTemplateDirectory());

        $server_data = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'QUERY_STRING' => '',
        ]);


        $Container->register('Environment', function() use ($Environment) {
            return $Environment;
        });

        $log_directory = $Environment->getLog();
        $Container->register('Logger', function() use ($Factory, $log_directory) {
            return $Factory->buildLogger($log_directory);
        });

        $Container->register('Request', function() use ($Factory, $server_data) {
            return $Factory->buildRequest($server_data, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            $Headers = $Factory->buildHttpHeaderCollection();
            return $Factory->buildResponse($Headers);
        });

        $Container->register('ConfigManager', function() use ($Factory) {
            /**
             * @var \Everon\Interfaces\Environment $Environment
             */
            $Environment = $Factory->getDependencyContainer()->resolve('Environment');
            $config_cache_directory = $Environment->getCacheConfig();
            $Matcher = $Factory->buildConfigExpressionMatcher();
            $Loader = $Factory->buildConfigLoader($Environment->getConfig(), $config_cache_directory);
            return $Factory->buildConfigManager($Loader, $Matcher);
        });

        $Container->register('Router', function() use ($Factory) {
            $Request = $Factory->getDependencyContainer()->resolve('Request');
            $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getRouterConfig();
            $RouterValidator = $Factory->buildRouterValidator();

            if ($RouteConfig == null) {
                mpr($RouteConfig);
            }
            return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
        });

        $Container->register('ModelManager', function() use ($Factory) {
            $Config = $Factory->getDependencyContainer()->resolve('ConfigManager')->getApplicationConfig();
            return $Factory->buildModelManager(
                $Config->go('model')->get('manager', 'Everon')
            );
        });

        $Container->register('ViewManager', function() use ($Factory) {
            $ApplicationConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getApplicationConfig();

            return $Factory->buildViewManager(
                $ApplicationConfig->go('view')->get('compilers', []),
                $Factory->getDependencyContainer()->resolve('Environment')->getViewTemplate(),
                $Factory->getDependencyContainer()->resolve('Environment')->getWebCache()
            );
        });
        
        return $Factory;
    }

    
}