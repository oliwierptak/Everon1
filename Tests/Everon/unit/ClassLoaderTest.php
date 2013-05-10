<?php
namespace Everon\Test;

class ClassLoaderTest extends \Everon\TestCase
{
    public function setUp()
    {
        $Loader = new \Everon\ClassLoader(null);
        $Loader->unRegister();
    }
    
    public function testConstructor()
    {
        $ClassMap = new \Everon\ClassMap();
        $Loader = new \Everon\ClassLoader($ClassMap);
        $this->assertInstanceOf('\Everon\Interfaces\ClassLoader', $Loader);
    }

    /**
     * @dataProvider dataProvider
     */    
    public function testLoadShouldIncludeFile(\Everon\Interfaces\ClassLoader $Loader)
    {
        $Loader->add('Everon', ev_DIR_SRC);
        $Loader->load('Everon\Core');
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \RuntimeException
     * @expectedExceptionMessagbe File for class: "test" could not be found
     */
    public function testLoadShouldThrowExceptionWhenFileWasNotFound(\Everon\Interfaces\ClassLoader $Loader)
    {
        $Loader->load('test_wrong_class');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIncludeFile(\Everon\Interfaces\ClassLoader $Loader)
    {
        $Loader->add('Everon', ev_DIR_SRC);
        $Loader->add('Everon\View', ev_DIR_VIEW);
        
        $method = $this->getProtectedMethod('Everon\ClassLoader', 'includeFile');
        $result = $method->invoke($Loader, 'Everon\Core', 'Everon', ev_DIR_SRC);
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\Model\User', 'Everon\Model', ev_DIR_MODEL);
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\View\Login', 'Everon\View', ev_DIR_VIEW);
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\Controller\Login', 'Everon\Controller', ev_DIR_CONTROLLER);
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\WrongClass', 'Everon\Wrong', ev_DIR_ROOT);
        $this->assertFalse($result);
    }

    public function dataProvider()
    {
        $Loader = new \Everon\ClassLoader(null);
        
        return [
            [$Loader]
        ];
    }

}

