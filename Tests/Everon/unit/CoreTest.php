<?php
namespace Everon\Test;

class CoreTest extends \Everon\TestCase
{
    
    protected function setUp()
    {
        parent::setUp();
        
        if (!is_dir($this->getLogDirectory())) {
            @mkdir($this->getLogDirectory(), 0775, true);
        }
    }
    
    public function testConstructor()
    {
        $Core = new \Everon\Core();
        $Core->shutdown();
        $this->assertInstanceOf('\Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRun(\Everon\Interfaces\Controller $Controller, \Everon\Interfaces\Factory $Factory)
    {
        $Core = $Factory->buildCore();
        $result = $Core->run($Controller);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\InvalidControllerMethod
     */
    public function testRunShouldThrowExceptionWhenInvalidControllerMethod(\Everon\Interfaces\Controller $Controller, \Everon\Interfaces\Factory $Factory)
    {
        $RouteItemMock = $this->getMock('Everon\Interfaces\RouteItem');
        $RouteItemMock->expects($this->atLeastOnce())
            ->method('getAction')
            ->will($this->returnValue('WrongActionName'));

        $RouterMock = $this->getMock('Everon\Interfaces\Router');
        $RouterMock->expects($this->atLeastOnce())
            ->method('getCurrentRoute')
            ->will($this->returnValue($RouteItemMock));

        $ControllerMock = $this->getMock('Everon\Test\MyController', [], [], '', false);
        $ControllerMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MyController'));

        $ControllerMock->expects($this->atLeastOnce())
            ->method('getRouter')
            ->will($this->returnValue($RouterMock));


        $Core = $Factory->buildCore();
        $result = $Core->run($ControllerMock);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBeforeRun(\Everon\Interfaces\Controller $Controller, \Everon\Interfaces\Factory $Factory)
    {
        $View = $this->getMock('\Everon\Interfaces\View');

        $RouteItemMock = $this->getMock('Everon\Interfaces\RouteItem');
        $RouteItemMock->expects($this->atLeastOnce())
            ->method('getAction')
            ->will($this->returnValue('testOne'));

        $RouterMock = $this->getMock('Everon\Interfaces\Router');
        $RouterMock->expects($this->atLeastOnce())
            ->method('getCurrentRoute')
            ->will($this->returnValue($RouteItemMock));

        $ControllerMock = $this->getMock('Everon\Test\MyController', [], [], '', false);
        $ControllerMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MyController'));

        $ControllerMock->expects($this->atLeastOnce())
            ->method('getRouter')
            ->will($this->returnValue($RouterMock));

        $ControllerMock->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($View));

        $ControllerMock->expects($this->once())
            ->method('beforeTestOne')
            ->will($this->returnValue(false));

        $ControllerMock->expects($this->never())
            ->method('testOne')
            ->will($this->returnValue(true));

        $Core = $Factory->buildCore();
        $result = $Core->run($ControllerMock);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAfterRun(\Everon\Interfaces\Controller $Controller, \Everon\Interfaces\Factory $Factory)
    {
        $View = $this->getMock('\Everon\Interfaces\View');

        $RouteItemMock = $this->getMock('Everon\Interfaces\RouteItem');
        $RouteItemMock->expects($this->atLeastOnce())
            ->method('getAction')
            ->will($this->returnValue('testOne'));

        $RouterMock = $this->getMock('Everon\Interfaces\Router');
        $RouterMock->expects($this->atLeastOnce())
            ->method('getCurrentRoute')
            ->will($this->returnValue($RouteItemMock));

        $ControllerMock = $this->getMock('Everon\Test\MyController', [], [], '', false);
        $ControllerMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MyController'));

        $ControllerMock->expects($this->atLeastOnce())
            ->method('getRouter')
            ->will($this->returnValue($RouterMock));

        $ControllerMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($View));

        $ControllerMock->expects($this->once())
            ->method('beforeTestOne')
            ->will($this->returnValue(true));

        $ControllerMock->expects($this->once())
            ->method('testOne')
            ->will($this->returnValue(true));

        $ControllerMock->expects($this->once())
            ->method('afterTestOne')
            ->will($this->returnValue(false));

        $Core = $Factory->buildCore();
        $result = $Core->run($ControllerMock);
        $this->assertFalse($result);
    }

    public function dataProvider()
    {
        $Logger = new \Everon\Logger($this->getLogDirectory());

        $Container = new \Everon\Dependency\Container();
        $Container->register('Logger', function() use ($Logger) {
            return $Logger;
        });        
        
        $MyFactory = new MyFactory($Container);

        $server = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'QUERY_STRING' => '',
        ]);
        $Request = new \Everon\Request($server, [], [], []);
        $Container->register('Request', function() use ($Request) {
            return $Request;
        });

        $Matcher = $MyFactory->buildConfigExpressionMatcher();
        $ConfigManager = $MyFactory->buildConfigManager($Matcher, $this->getConfigDirectory(), $this->getTempDirectory().'configmanager'.ev_DS);
        $Container->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });

        $RouterConfig = $ConfigManager->getRouterConfig();
        $Router = $MyFactory->buildRouter($Request, $RouterConfig);
        $Container->register('Router', function() use ($Router) {
            return $Router;
        });

        $Config = $Container->resolve('ConfigManager')->getApplicationConfig();
        $Container->register('ModelManager', function() use ($MyFactory, $Config) {
            return $MyFactory->buildModelManager($Config->go('model')->get('manager'));
        });        

        $Container->register('Response', function() use ($MyFactory) {
            return $MyFactory->buildResponse();
        });        

        $View = $MyFactory->buildView('MyController', ['Curly']);
        $Controller = $MyFactory->buildController('MyController', $View, $Container->resolve('ModelManager'));

        return [
            [$Controller, $MyFactory]
        ];
    }

}

