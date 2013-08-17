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
            'MyModel.php',
            'MyModelManager.php',
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

    /**
     * @return Interfaces\Factory
     */
    public function getFactory()
    {
        $Factory = new Factory(new Dependency\Container());
        $Container = $Factory->getDependencyContainer();
        $Environment = $this->Environment;
        
        $Environment->setLog($this->getLogDirectory());
        $Environment->setConfig($this->getConfigDirectory());
        $Environment->setCacheConfig($this->getConfigCacheDirectory());
        $Environment->setViewTemplate($this->getTemplateDirectory());

        $Container->register('Environment', function() use ($Environment) {
            return $Environment;
        });

        $Container->register('Request', function() use ($Factory) {
            $server_data = $this->getServerDataForRequest([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/',
                'QUERY_STRING' => '',
            ]);
            
            return $Factory->buildRequest($server_data, $_GET, $_POST, $_FILES);
        });

        require($Environment->getEveronLib().'Dependencies.php');
        
        return $Factory;
    }

    
}