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
        $View = $this->getMock('Everon\Interfaces\View');
        
        $ViewManager = $this->getMockBuilder('Everon\Interfaces\ViewManager')
            ->disableOriginalConstructor()
            ->getMock();

        $ViewManager->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($View));

        $ModelManager = $this->getMockBuilder('Everon\Interfaces\ModelManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $Controller = new \Everon\Test\MyController($ViewManager, $ModelManager);
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
        
        $Controller->getModelManager()->getModel('wrong_model_name');
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

        $Model = $Controller->getModelManager()->getModel('MyModel');
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
        
        $ViewMock->expects($this->once())
            ->method('setOutput');
        
        $Controller->getView()->setOutput('test');
        $this->assertEquals('test', (string) $Controller->getView()->getOutput());
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->getFactory();
        
        $View = $this->getMockBuilder('Everon\Interfaces\View')
            ->disableOriginalConstructor()
            ->getMock();
        
        $ViewManager = $this->getMockBuilder('Everon\Interfaces\ViewManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $ViewManager->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($View));
        
        $ModelManager = $this->getMockBuilder('Everon\Interfaces\ModelManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $Controller = $Factory->buildController('MyController', 'Everon\Test');
        $Controller->setModelManager($ModelManager);
        $Controller->setViewManager($ViewManager);

        return [
            [$Controller]
        ];
    }

}