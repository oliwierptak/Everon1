<?php
namespace Everon\Test;

class ControllerTest extends \Everon\TestCase
{

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Model: "\Everon\Test\wrong_model_name" initialization error. 
     * File for class: "Everon\Test\wrong_model_name" could not be found
     */
    public function testGetModelShouldThrowAnExceptionWhenInvalidModelName(\Everon\Interfaces\Controller $Controller)
    {
        $Controller->getModel('wrong_model_name');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetModelShouldReturnValidModel(\Everon\Interfaces\Controller $Controller)
    {
        $Model = $Controller->getModel('MyModel');
        $this->assertNotNull($Model);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGettersAndSetters(\Everon\Interfaces\Controller $Controller)
    {
        $this->assertEquals('Everon\Test\MyController', $Controller->getName());
        
        $Controller->setName('test');
        $this->assertEquals('test', $Controller->getName());

        $Controller->setOutput('test');
        $this->assertEquals('test', $Controller->getOutput());

        $this->assertInstanceOf('\Everon\Interfaces\Request', $Controller->getRequest());
        $this->assertInstanceOf('\Everon\Interfaces\Router', $Controller->getRouter());
        $this->assertInstanceOf('\Everon\Interfaces\ConfigManager', $Controller->getConfigManager());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetOutputShouldRunInitViewWhenViewWasNotSet(\Everon\Interfaces\Controller $Controller)
    {
        $Controller->setName('test');
        $this->assertEquals('test', $Controller->getName());
        
        $this->assertInstanceOf('\Everon\Interfaces\View', $Controller->getView());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShouldReturnOutputWhenCastedToString(\Everon\Interfaces\Controller $Controller)
    {
        $Controller->setOutput('test');
        $this->assertEquals('test', (string) $Controller);
    }
    
    /**
     * @runInSeparateProcess
     * @backupStaticAttributes disabled
     * @dataProvider dataProvider
     */
    public function AAtestResultShouldDisplayOutput(\Everon\Interfaces\Controller $Controller)
    {
        $headers = xdebug_get_headers();
        $Response = new \Everon\Response(['test'], new \Everon\Http\HeaderCollection());
        $Controller->result($Response);
        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $Controller->setFactory($FactoryMock);
        //$this->assertEquals('test', (string) $Controller);
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

        $Container->register('Response', function() use ($MyFactory) {
            return $MyFactory->buildResponse();
        });

        $Config = $Container->resolve('ConfigManager')->getApplicationConfig();
        $Container->register('ModelManager', function() use ($MyFactory, $Config) {
            return $MyFactory->buildModelManager($Config->go('model')->get('manager'));
        });

        $View = $MyFactory->buildView('MyController', ['Curly']);
        $Controller = $MyFactory->buildController('MyController', $View, $Container->resolve('ModelManager'));
        
        return [
            [$Controller]
        ];
    }

}