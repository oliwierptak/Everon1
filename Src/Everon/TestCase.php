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

    protected $backupGlobalsBlacklist = array('Container', 'Factory');

    /**
     * @var Interfaces\Environment
     */
    protected $Environment = null;


    public function __construct($name = NULL, array $data=[], $dataName='')
    {
        parent::__construct($name, $data, $dataName);

        $nesting = implode('..', array_fill(0, 3, DIRECTORY_SEPARATOR));
        $root = realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR;
        
        $this->Environment = new Environment($root);
        
        $dir = $this->getDoublesDirectory();
        $Doubles = new \GlobIterator($dir.'*.*');
        foreach ($Doubles as $filename => $Include) {
            include_once($Include->getPathname());
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $directories = [
            $this->getTmpDirectory(),
            $this->getConfigCacheDirectory(),
            $this->getLogDirectory(),
        ];
        
        foreach ($directories as $dir) {
            $TmpFiles = new \GlobIterator($dir.'*.*');
            foreach ($TmpFiles as $filename => $File) {
                @unlink($File->getPathname());
            }
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

    public function getTmpDirectory()
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
        return $this->getTmpDirectory().'logs'.DIRECTORY_SEPARATOR;
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
        return $this->getTmpDirectory().'config'.DIRECTORY_SEPARATOR;
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

    /**
     * @return Interfaces\Factory
     */
    public function getFactory()
    {
        $Factory = new Factory(new Dependency\Container());
        $Container = $Factory->getDependencyContainer();

        $TestEnvironment = new \Everon\Environment($this->Environment->getRoot());
        $TestEnvironment->setLog($this->getLogDirectory());
        $TestEnvironment->setConfig($this->getConfigDirectory());
        $TestEnvironment->setCacheConfig($this->getConfigCacheDirectory());
        $TestEnvironment->setTmp($this->getTmpDirectory());

        require($this->Environment->getEveronLib().'Dependencies.php');

        $Container->register('Environment', function() use ($TestEnvironment) {
            return $TestEnvironment;
        });

        $Container->register('Request', function() use ($Factory) {
            $server_data = $this->getServerDataForRequest([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'QUERY_STRING' => '',
            ]);
            
            return $Factory->buildRequest($server_data, $_GET, $_POST, $_FILES);
        });

        return $Factory;
    }

    
}