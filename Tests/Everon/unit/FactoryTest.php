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
use Everon\Exception;

class FactoryTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $FactoryInstance = new \Everon\Factory(new \Everon\Dependency\Container());
        $this->assertInstanceOf('Everon\Interfaces\Factory', $FactoryInstance);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfigShouldSilentlyFallBackToDefaultConfigWhenClassNotFound(Interfaces\Factory $Factory)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', []);
        $Config = $Factory->buildConfig('test', $Item, $Compiler);
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfig(Interfaces\Factory $Factory)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', []);
        $Config = $Factory->buildConfig('test', $Item, $Compiler);
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildMvcCore(Interfaces\Factory $Factory)
    {
        $Core = $Factory->buildMvc();
        $this->assertInstanceOf('Everon\Interfaces\Core', $Core);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildConfigManager(Interfaces\Factory $Factory)
    {
        $Matcher = $this->getMock('Everon\Interfaces\ConfigExpressionMatcher');
        $Loader = $this->getMock('Everon\Interfaces\ConfigLoader');
        $ConfigManager = $Factory->buildConfigManager($Loader, $Matcher);
        $this->assertInstanceOf('Everon\Interfaces\ConfigManager', $ConfigManager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildMvcController(Interfaces\Factory $Factory)
    {
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });

        $RouterMock = $this->getMock('Everon\Interfaces\Router');
        $Factory->getDependencyContainer()->register('Router', function() use ($RouterMock) {
            return $RouterMock;
        });
        
        $ViewManagerMock = $this->getMock('Everon\Interfaces\ViewManager');
        $Factory->getDependencyContainer()->register('ViewManager', function() use ($ViewManagerMock) {
            return $ViewManagerMock;
        });

        $DomainManagerMock = $this->getMock('Everon\Domain\Interfaces\Manager', [],[],'', false);
        $Factory->getDependencyContainer()->register('DomainManager', function() use ($DomainManagerMock) {
            return $DomainManagerMock;
        });

        $Controller = $Factory->buildController('MyController', 'Everon\Test');
        $this->assertInstanceOf('Everon\Interfaces\Controller', $Controller);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildView(Interfaces\Factory $Factory)
    {
        $TemplateMock = $this->getMock('Everon\Interfaces\Template', [], [], '', false);
        $View = $Factory->buildView('MyView', $this->getTemplateDirectory(), [], $TemplateMock, '.htm','Everon\Test');
        $this->assertInstanceOf('Everon\Interfaces\View', $View);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildModel(Interfaces\Factory $Factory)
    {
        $ConfigManager = $this->getMock('Everon\Interfaces\ConfigManager');
        $Factory->getDependencyContainer()->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });
        
        $Model = $Factory->buildDomainModel('User', 'Everon\Test\Domain');
        $this->assertInstanceOf('Everon\Test\Domain\User\Model', $Model);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildDomainManager(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Not sure about the DomainManager\'s dependencies');
        $ConfigManager = $this->getMock('Everon\Interfaces\ConfigManager');
        $Factory->getDependencyContainer()->register('ConfigManager', function() use ($ConfigManager) {
            return $ConfigManager;
        });
        
        $DomainManager = $Factory->buildDomainManager('MyDomainManager', 'Everon\Test');
        $this->assertInstanceOf('Everon\Domain\Interfaces\Manager', $DomainManager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildRouter(Interfaces\Factory $Factory)
    {
        $RequestMock = $this->getMock('Everon\Interfaces\Request');
        $Factory->getDependencyContainer()->register('Request', function() use ($RequestMock) {
            return $RequestMock;
        });
        $RouterConfig = $this->getMock('Everon\Config\Router', [], [], '', false);

        $Router = $Factory->buildRouter($RouterConfig, $Factory->buildRequestValidator());
        $this->assertInstanceOf('Everon\Interfaces\Router', $Router);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildRouteItem(Interfaces\Factory $Factory)
    {
        $RouteItem = $Factory->buildConfigItemRouter('test', [
            'url' => '/test.htm',
            'controller' => 'MyController',
            'action' => 'testOne',
            'params' => [],
            'parsed_query_data' => [],
            'default' => false,
        ]);

        $this->assertInstanceOf('Everon\Interfaces\ConfigItemRouter', $RouteItem);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildTemplate(Interfaces\Factory $Factory)
    {
        $Template = $Factory->buildTemplate('', []);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $Template);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildTemplateContainer(Interfaces\Factory $Factory)
    {
        $TemplateContainer = $Factory->buildTemplateContainer('', []);
        $this->assertInstanceOf('Everon\Interfaces\TemplateContainer', $TemplateContainer);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBuildLogger(Interfaces\Factory $Factory)
    {
        $Logger = $Factory->buildLogger($this->getLogDirectory(), true);
        $this->assertInstanceOf('Everon\Interfaces\Logger', $Logger);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildResponse(Interfaces\Factory $Factory)
    {
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection');
        $Response = $Factory->buildResponse('guid', $HeadersMock);
        $this->assertInstanceOf('Everon\Interfaces\Response', $Response);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildHttpHeaderCollection(Interfaces\Factory $Factory)
    {
        $HeaderCollection = $Factory->buildHttpHeaderCollection();
        $this->assertInstanceOf('Everon\Interfaces\Collection', $HeaderCollection);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testBuildRequest(Interfaces\Factory $Factory)
    {
        $server = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'QUERY_STRING' => '',
        ]);
        
        $Request = $Factory->buildRequest($server, [], [], []);
        $this->assertInstanceOf('Everon\Interfaces\Request', $Request);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Error injecting dependency: "Wrong"
     */    
    public function testDependencyToObjectShouldThrowExceptionWhenWrongDependency(Interfaces\Factory $Factory)
    {
        $Wrong = new \stdClass();
        $Wrong = $Factory->getDependencyContainer()->inject('Wrong', $Wrong);
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Console initialization error
     */
    public function testBuildCoreConsoleShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildConsole();
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Config: "test_config" initialization error
     */
    public function testBuildConfigShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $Compiler = function(){};
        $Item = new \Everon\Config\Loader\Item('wrong_filename', []);
        $FactoryMock->buildConfig('test_config', $Item, $Compiler);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage ConfigManager initialization error
     */
    public function testBuildConfigManagerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $Matcher = $this->getMock('Everon\Interfaces\ConfigExpressionMatcher');
        $Loader = $this->getMock('Everon\Interfaces\ConfigLoader');
        $FactoryMock->buildConfigManager($Loader, $Matcher);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Controller: "Everon\Controller\Test" initialization error.
     * File for class: "Everon\Controller\Test" could not be found
     */
    public function testBuildControllerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $ViewManager = $this->getMock('Everon\Interfaces\ViewManager');
        $DomainManager = $this->getMock('Everon\Domain\Interfaces\Manager');
        $FactoryMock->buildController('Test');
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage View: "Everon\Test\Wrong" initialization error.
     * TemplateCompiler: "Everon\View\Template\Compiler\NonExisting" initialization error.
     * File for class: "Everon\View\Template\Compiler\NonExisting" could not be found
     */
    public function testBuildViewShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $TemplateMock = $this->getMock('Everon\Interfaces\Template', [], [], '', false);
        $Factory->buildView('Wrong', $this->getTemplateDirectory(), [], $TemplateMock, '.htm', 'Everon\Test');
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Model: "Everon\Model\DummyNotExisting" initialization error.
     * File for class: "Everon\Model\DummyNotExisting" could not be found
     */
    public function testBuildModelShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Domain under construction');
        $Factory->buildDomainModel('DummyNotExisting');
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage DomainManager: "Everon\Model\Handler\Test" initialization error.
     * File for class: "Everon\Model\Handler\Test" could not be found
     */
    public function testBuildDomainManagerShouldThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $this->markTestSkipped('Domain under construction');
        $Factory->buildDomainManager('Test');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Router initialization error
     */
    public function testBuildRouterShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $Config = $this->getMock('Everon\Interfaces\Config');
        $Validator = $this->getMock('Everon\Interfaces\RequestValidator');
        $FactoryMock->buildRouter($Config, $Validator);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage ConfigItemRouter: "test" initialization error
     */
    public function testBuildRouteItemThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildConfigItemRouter('test', []);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Template initialization error
     */
    public function testBuildTemplateThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $View = $this->getMock('Everon\Interfaces\View');
        $FactoryMock->buildTemplate('', []);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage TemplateContainer initialization error
     */
    public function testBuildTemplateContainerThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildTemplateContainer('', []);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage TemplateCompiler: "Everon\View\Template\Compiler\Test" initialization error.
     * File for class: "Everon\View\Template\Compiler\Test" could not be found
     */
    public function testBuildTemplateCompilerThrowExceptionWhenWrongClass(Interfaces\Factory $Factory)
    {
        $Factory->buildTemplateCompiler('Test');
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Logger initialization error
     */
    public function testBuildLoggerThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildLogger($this->getLogDirectory(), false);
    }

    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Response initialization error
     */
    public function testBuildResponseShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection');
        $FactoryMock->buildResponse('guid', $HeadersMock);
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage HttpHeaderCollection initialization error
     */
    public function testBuildHttpHeaderCollectionShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildHttpHeaderCollection();
    }
    
    /**
     * @dataProvider dataProviderForExceptions
     * @expectedException \Everon\Exception\Factory
     * @expectedExceptionMessage Request initialization error
     */
    public function testBuildRequestShouldThrowExceptionWhenWrongClass(Interfaces\Factory $FactoryMock)
    {
        $FactoryMock->buildRequest([], [], [], []);
    }

    public function dataProvider()
    {
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $ViewManager = $Factory->buildViewManager(['e' => '.htm'], $this->Environment->getView());
        $Container->register('ViewManager', function() use ($ViewManager) {
            return $ViewManager;
        });
        
        return [
            [$Factory]
        ];
    }
    
    public function dataProviderForExceptions()
    {
        $FactoryMock = $this->getMockBuilder('Everon\Factory')->disableOriginalConstructor()
            ->setMethods(['getDependencyContainer'])
            ->getMock();

        $FactoryMock->expects($this->once())
            ->method('getDependencyContainer')
            ->will($this->throwException(new \Exception));
        
        return [
            [$FactoryMock]
        ];
    }



}