<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
        $ClassMap = new \Everon\ClassMap('');
        $Loader = new \Everon\ClassLoader($ClassMap);
        $this->assertInstanceOf('\Everon\Interfaces\ClassLoader', $Loader);
    }

    /**
     * @dataProvider dataProvider
     */    
    public function testLoadShouldIncludeFile(\Everon\Interfaces\ClassLoader $Loader, \Everon\Interfaces\Environment $Environment)
    {
        $Loader->add('Everon', $Environment->getSource());
        $Loader->load('Everon\Core');
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \RuntimeException
     * @expectedExceptionMessagbe File for class: "test" could not be found
     */
    public function testLoadShouldThrowExceptionWhenFileWasNotFound(\Everon\Interfaces\ClassLoader $Loader, \Everon\Interfaces\Environment $Environment)
    {
        $Loader->load('test_wrong_class');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIncludeFile(\Everon\Interfaces\ClassLoader $Loader, \Everon\Interfaces\Environment $Environment)
    {
        $Loader->add('Everon', $Environment->getSource());
        $Loader->add('Everon\View', $Environment->getView());
        
        $method = $this->getProtectedMethod('Everon\ClassLoader', 'includeFile');
        $result = $method->invoke($Loader, 'Everon\Core', 'Everon', $Environment->getSource());
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\Model\User', 'Everon\Model', $Environment->getModel());
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\View\Login', 'Everon\View', $Environment->getView());
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\Controller\Login', 'Everon\Controller', $Environment->getController());
        $this->assertTrue($result !== false);

        $result = $method->invoke($Loader, 'Everon\WrongClass', 'Everon\Wrong', $Environment->getRoot());
        $this->assertFalse($result);
    }

    public function dataProvider()
    {
        $Loader = new \Everon\ClassLoader(null);
        $Environment = new \Everon\Environment(PROJECT_ROOT);
        
        return [
            [$Loader, $Environment]
        ];
    }

}

