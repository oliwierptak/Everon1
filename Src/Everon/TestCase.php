<?php
namespace Everon;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    
    protected $suite_name = 'Everon'; //todo: this should come from PHPUnit enviroment

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $dir = $this->getDoublesDirectory();
        $includes = [
            'MyFactory.php',
            'MyController.php',
            'MyModelManager.php',
            'MyModel.php',
            'MyView.php'
        ];
        
        for ($a=0; $a<count($includes); ++$a) {
            include_once($dir.$includes[$a]);
        }
    }

    protected function removeDirectoryRecursive($dir)
    {
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
        return ev_DIR_TESTS.$this->suite_name.ev_DS.'tmp'.ev_DS;
    }

    public function getFixtureDirectory()
    {
        return ev_DIR_TESTS.$this->suite_name.ev_DS.'fixtures'.ev_DS;
    }

    public function getStubDirectory()
    {
        return ev_DIR_TESTS.$this->suite_name.ev_DS.'stubs'.ev_DS;
    }

    public function getMockDirectory()
    {
        return ev_DIR_TESTS.$this->suite_name.ev_DS.'mocks'.ev_DS;
    }

    public function getDoublesDirectory()
    {
        return ev_DIR_TESTS.$this->suite_name.ev_DS.'doubles'.ev_DS;
    }

    public function getLogDirectory()
    {
        return $this->getTempDirectory().'logs'.ev_DS;
    }

    public function getConfigDirectory()
    {
        return $this->getFixtureDirectory().'config'.ev_DS;
    }
    
    public function getTemplateDirectory()
    {
        return $this->getFixtureDirectory().'templates'.ev_DS;
    }

    protected function getConfigManagerTempDirectory()
    {
        return $this->getTempDirectory().'configmanager'.ev_DS;
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
    
}