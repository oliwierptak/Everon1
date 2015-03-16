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
    
    protected $suite_name = 'Everon';

    protected $data_fixtures = null;

    /**
     * @var Bootstrap
     */
    protected $FrameworkBootstrap = null;

    /**
     * @var \Everon\RequestIdentifier
     */
    protected $RequestIdentifier = null;
    

    public function __construct($name = NULL, array $data=[], $dataName='')
    {
        //todo: remove global state
        
        $Dir = new \SplFileInfo(@$GLOBALS['EVERON_ROOT']);
        if ($Dir->isDir() === false) {
            throw new Exception\Core('Everon root directory is not defined');
        }
        
        $Dir = new \SplFileInfo(@$GLOBALS['EVERON_SOURCE_ROOT']);
        if ($Dir->isDir() === false) {
            throw new Exception\Core('Everon source directory is not defined');
        }

        parent::__construct($name, $data, $dataName);

        $custom_test_paths = array_merge([
            'config' => getcwd().'/fixtures/config/',
            'tmp' => getcwd().'/tmp/',
        ], @$GLOBALS['EVERON_CUSTOM_PATHS']);
        
        $this->RequestIdentifier = @$GLOBALS['EVERON_REQUEST_IDENTIFIER'];
        $Environment = new Environment(@$GLOBALS['EVERON_ROOT'], @$GLOBALS['EVERON_SOURCE_ROOT'], EVERON_ENVIRONMENT, $custom_test_paths);
        $this->FrameworkBootstrap = new Bootstrap($Environment, 'development');
        
        $this->includeDoubles();

        unset($GLOBALS['EVERON_FACTORY']);
        unset($GLOBALS['EVERON_BOOTSTRAP']);
        //unset($GLOBALS['EVERON_CUSTOM_PATHS']);
        //unset($GLOBALS['EVERON_REQUEST_IDENTIFIER']);
        //unset($GLOBALS['CUSTOM_EXCEPTION_HANDLER']);
    }

    protected function includeDoubles()
    {
        $includes = new \GlobIterator($this->getDoublesDirectory().'*.php');
        foreach ($includes as $filename => $IncludeItem) {
            if ($IncludeItem->isFile() && $IncludeItem->getExtension() === 'php') {
                require_once($IncludeItem->getPathname());
            }
        }
    }

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

    /**
     * @param $data
     * @return array
     */
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
    
    public function getTmpDirectory()
    {
        return $this->getFrameworkBootstrap()->getEnvironment()->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
    }

    public function getFixtureDirectory()
    {
        return $this->getFrameworkBootstrap()->getEnvironment()->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR;
    }

    public function getDoublesDirectory()
    {
        return $this->getFrameworkBootstrap()->getEnvironment()->getTest().$this->suite_name.DIRECTORY_SEPARATOR.'doubles'.DIRECTORY_SEPARATOR;
    }

    public function getTemplateDirectory()
    {
        return $this->getFixtureDirectory().'templates'.DIRECTORY_SEPARATOR;
    }

    protected function getViewCacheDirectory()
    {
        return $this->getTmpDirectory().'view'.DIRECTORY_SEPARATOR;
    }

    /**
     * @param $class_name
     * @param $name
     * @return \ReflectionProperty
     */
    public function getProtectedProperty($class_name, $name)
    {
        $Reflection = new \ReflectionClass($class_name);
        $Property = $Reflection->getProperty($name);
        $Property->setAccessible(true);
        
        return $Property;
    }

    /**
     * @param $class_name
     * @param $name
     * @return \ReflectionMethod
     */
    public function getProtectedMethod($class_name, $name)
    {
        $Reflection = new \ReflectionClass($class_name);
        $Method = $Reflection->getMethod($name);
        $Method->setAccessible(true);
        
        return $Method;
    }

    /**
     * @return Bootstrap
     */
    public function getFrameworkBootstrap()
    {
        return $this->FrameworkBootstrap;
    }

    /**
     * @param Bootstrap $FrameworkBootstrap
     */
    public function setFrameworkBootstrap($FrameworkBootstrap)
    {
        $this->FrameworkBootstrap = $FrameworkBootstrap;
    }

    /**
     * @return RequestIdentifier
     */
    public function getRequestIdentifier()
    {
        return $this->RequestIdentifier;
    }

    /**
     * @param RequestIdentifier $RequestIdentifier
     */
    public function setRequestIdentifier($RequestIdentifier)
    {
        $this->RequestIdentifier = $RequestIdentifier;
    }
    
    /**
     * @return Interfaces\Factory
     */
    public function buildFactory()
    {
        /**
         * @var \Everon\Interfaces\Factory $EVERON_FACTORY
         */
        $EVERON_FACTORY = new Application\Factory(new Application\Dependency\Container());
        $EVERON_CONTAINER = $EVERON_FACTORY->getDependencyContainer();

        //$TestEnvironment = new \Everon\Environment($this->FrameworkBootstrap->getEnvironment()->getRoot(), $this->FrameworkBootstrap->getEnvironment()->getEveronRoot());
        $TestEnvironment = $this->FrameworkBootstrap->getEnvironment();
        
        /*
        $TestEnvironment->setLog($this->getLogDirectory());
        $TestEnvironment->setConfig($this->getFixtureDirectory().'config'.DIRECTORY_SEPARATOR);
        $TestEnvironment->setCacheConfig($this->getConfigCacheDirectory());
        $TestEnvironment->setTmp($this->getTmpDirectory());
        $TestEnvironment->setDomainConfig($this->getFixtureDirectory().'Domain/');
        */

        $EVERON_BOOTSTRAP = $this->FrameworkBootstrap;
        $EVERON_CONTAINER->register('Bootstrap', function() use ($EVERON_BOOTSTRAP) {
            return $EVERON_BOOTSTRAP;
        });

        $EVERON_CONTAINER->register('Environment', function() use ($TestEnvironment) {
            return $TestEnvironment;
        });

        $FileSystem = $EVERON_FACTORY->buildFileSystem($TestEnvironment->getRoot());
        $EVERON_CONTAINER->register('FileSystem', function() use ($FileSystem) {
            return $FileSystem;
        });

        $EVERON_CONTAINER->register('RequestIdentifier', function()  {
            return $this->RequestIdentifier;
        });

        $ConfigLoader = $EVERON_FACTORY->buildConfigLoader($TestEnvironment->getConfig(), $TestEnvironment->getConfigFlavour());
        $ConfigLoader->setFactory($EVERON_FACTORY);

        $ConfigLoaderCache = $EVERON_FACTORY->buildConfigCacheLoader($TestEnvironment->getCache());
        $ConfigLoaderCache->setFactory($EVERON_FACTORY);

        $ConfigManager = $EVERON_FACTORY->buildConfigManager($ConfigLoader, $ConfigLoaderCache);
        $ConfigManager->setFactory($EVERON_FACTORY);
        $ConfigManager->setFileSystem($FileSystem);
        
        $EVERON_CONTAINER->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });

        require($this->FrameworkBootstrap->getEnvironment()->getEveronConfig().'_dependencies.php');

        //register global unique (request) identifier with Logger
        /**
         * @var \Everon\Interfaces\Logger $Logger
         */
        $Logger = $EVERON_CONTAINER->resolve('Logger');
        $Logger->setRequestIdentifier($this->RequestIdentifier->getValue());

        $EVERON_CONTAINER->register('Request', function() use ($EVERON_FACTORY) {
            $server_data = $this->getServerDataForRequest([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'QUERY_STRING' => '',
            ]);
            
            return $EVERON_FACTORY->buildHttpRequest($server_data, $_GET, $_POST, $_FILES);
        });

        return $EVERON_FACTORY;
    }

}
