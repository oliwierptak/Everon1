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

class ControllerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $View = $this->getMockBuilder('Everon\Interfaces\View')
            ->disableOriginalConstructor()
            ->getMock();

        $ModelManager = $this->getMockBuilder('Everon\Interfaces\ModelManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $Controller = new \Everon\Test\MyController($View, $ModelManager);
        $this->assertInstanceOf('Everon\Interfaces\Controller', $Controller);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Model: "\Everon\Test\wrong_model_name" initialization error. 
     * File for class: "Everon\Test\wrong_model_name" could not be found
     */
    public function testGetModelShouldThrowAnExceptionWhenInvalidModelName(\Everon\Interfaces\Controller $Controller)
    {
        $ex = new \Everon\Exception\Factory(
            'Model: "\Everon\Test\wrong_model_name" initialization error.'."\n".'File for class: "Everon\Test\wrong_model_name" could not be found'
        );
        
        $ModelManagerMock = $Controller->getModelManager();
        $ModelManagerMock->expects($this->once())
            ->method('getModel')
            ->with('wrong_model_name')
            ->will($this->throwException($ex));
        
        $Controller->getModel('wrong_model_name');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetModelShouldReturnValidModel(\Everon\Interfaces\Controller $Controller)
    {
        $ModelManagerMock = $Controller->getModelManager();
        $ModelManagerMock->expects($this->once())
            ->method('getModel')
            ->with('MyModel')
            ->will($this->returnValue(new \stdClass()));

        $Model = $Controller->getModel('MyModel');
        $this->assertNotNull($Model);
    }
  
    /**
     * @dataProvider dataProvider
     */
    public function testResult(\Everon\Interfaces\Controller $Controller)
    {
        $this->markTestIncomplete();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShouldReturnOutputWhenCastedToString(\Everon\Interfaces\Controller $Controller)
    {
        $ViewMock = $Controller->getView();
        $ViewMock->expects($this->once())
            ->method('getOutput')
            ->will($this->returnValue('test'));
        
        $Controller->setOutput('test');
        $this->assertEquals('test', (string) $Controller);
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\DependencyContainer $Container
         * @var \Everon\Interfaces\Factory $Factory
         */        
        list($Container, $Factory) = $this->getContainerAndFactory();
        
        $View = $this->getMockBuilder('Everon\Interfaces\View')
            ->disableOriginalConstructor()
            ->getMock();
        
        $ModelManager = $this->getMockBuilder('Everon\Interfaces\ModelManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $Controller = $Factory->buildController('MyController', $View, $ModelManager, 'Everon\Test');
        
        return [
            [$Controller]
        ];
    }

}