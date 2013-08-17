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

use Everon\Interfaces;

class CoreTest extends \Everon\TestCase
{
    
    public function testConstructor()
    {
        $Core = new \Everon\Core\Mvc();
        $this->assertInstanceOf('\Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRun(Interfaces\Factory $Factory, Interfaces\Core $Mvc)
    {
        $Mvc->run();
        $this->assertInstanceOf('Everon\Interfaces\Core', $Mvc);
    }

    public function dataProvider()
    {
        /**
         * @var Interfaces\DependencyContainer $Container
         * @var Interfaces\Factory $Factory
         */
        $Factory = $this->getFactory();
        $Container = $Factory->getDependencyContainer();

        $HeadersMock = $this->getMockBuilder('Everon\Http\HeaderCollection')
            ->setConstructorArgs([])
            ->getMock();
        
        $ResponseMock = $this->getMockBuilder('Everon\Response')
            ->setConstructorArgs([$HeadersMock])
            ->getMock();
        $ResponseMock->expects($this->any())
            ->method('toHtml')
            ->will($this->returnValue('<html>'));
        $ResponseMock->expects($this->any())
            ->method('send')
            ->will($this->returnValue(true));
        
        $ViewMock = $this->getMockBuilder('Everon\View')
            ->setConstructorArgs([$this->Environment->getViewTemplate()])
            ->getMock();
        $ViewMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue([]));

        $ControllerMock = $this->getMockBuilder('Everon\Test\MyController')
            ->getMock();
        $ControllerMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($ViewMock));
        
        $ViewManagerMock = $this->getMockBuilder('Everon\Interfaces\ModelManager')
            ->disableOriginalConstructor()
            ->getMock();
        $ViewManagerMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($ViewMock));

        $FactoryMock = $this->getMockBuilder('Everon\Factory')
            ->setConstructorArgs([$Container])
            ->getMock();
        $FactoryMock->expects($this->any())
            ->method('buildController')
            ->will($this->returnValue($ControllerMock));
        $FactoryMock->expects($this->any())
            ->method('buildView')
            ->will($this->returnValue($ViewMock));
        $FactoryMock->expects($this->any())
            ->method('buildResponse')
            ->will($this->returnValue($ResponseMock));
        $FactoryMock->expects($this->any())
            ->method('buildViewManager')
            ->will($this->returnValue($ViewManagerMock));

        /**
         * @var \Everon\Core\Mvc $Mvc
         */
        $Mvc = $Factory->buildMvc();
        $Mvc->setFactory($FactoryMock);

        return [
            [$FactoryMock, $Mvc]
        ];
    }

}

