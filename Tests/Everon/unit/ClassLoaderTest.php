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
        $Loader = new \Everon\ClassLoader();
        $Loader->unRegister();
    }
    
    public function testConstructor()
    {
        $ClassMap = new \Everon\ClassMap('');
        $Loader = new \Everon\ClassLoader($ClassMap);
        $this->assertInstanceOf('Everon\Interfaces\ClassLoader', $Loader);
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

    public function dataProvider()
    {
        $Loader = new \Everon\ClassLoader(null);
        $Environment = $this->Environment;
        
        return [
            [$Loader, $Environment]
        ];
    }

}

