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

class RouterTest extends \Everon\TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testConstructor(\Everon\Interfaces\Factory $Factory, \Everon\Interfaces\Request $Request, \Everon\Config\Router $Config, $expected)
    {
        $Router = new \Everon\Router($Config, $Factory->buildRequestValidator());
        $this->assertInstanceOf('\Everon\Interfaces\Router', $Router);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\InvalidRoute
     */
    public function testPageNotFound(\Everon\Interfaces\Factory $Factory, \Everon\Interfaces\Request $Request, \Everon\Config\Router $Config, $expected)
    {
        $Request->setPath('/wrong/page/htm');
        $Router = $Factory->buildRouter($Config, $Factory->buildRequestValidator());

        $Item = $Router->getRouteByRequest($Request);
    }
    
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetRouteItemByRequest(\Everon\Interfaces\Factory $Factory, \Everon\Interfaces\Request $Request, \Everon\Config\Router $Config, $expected)
    {
        $Router = $Factory->buildRouter($Config, $Factory->buildRequestValidator());
        $Item = $Router->getRouteByRequest($Request);
        
        $this->assertInstanceOf('Everon\Interfaces\ConfigItemRouter', $Item);
        $this->assertEquals($Item->getController(), $expected['controller']);
        $this->assertEquals($Item->getAction(), $expected['action']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRouteItemByRequestShouldReturnDefault(\Everon\Interfaces\Factory $Factory, \Everon\Interfaces\Request $Request, \Everon\Config\Router $Config, $expected)
    {
        $Router = $Factory->buildRouter($Config, $Factory->buildRequestValidator());
        $Item = $Router->getRouteByRequest($Request);

        $this->assertInstanceOf('Everon\Interfaces\ConfigItemRouter', $Item);
        $this->assertEquals($Item->getController(), $expected['controller']);
        $this->assertEquals($Item->getAction(), $expected['action']);
    }

    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $RouterConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
        
        return [
            [$Factory,
                $Factory->buildRequest($this->getServerDataForRequest([
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI' => '/',
                    'QUERY_STRING' => '',
                    ]),
                    [],
                    [],
                    []
                ), 
                $RouterConfig, 
                ['controller'=>'\Everon\Test\MyController', 'action'=>'one']],
            [$Factory,
                $Factory->buildRequest($this->getServerDataForRequest([
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => '/one/two',
                    'QUERY_STRING' => '',
                    ]),
                    [],
                    ['username' => 'test', 'password' => 'test123'],
                    []
                ), 
                $RouterConfig, 
                ['controller'=>'\Everon\Test\MyController', 'action'=>'two']],
            [$Factory,
                $Factory->buildRequest($this->getServerDataForRequest([
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => '/login/submit/session/adf24ds34/redirect/account%5Csummary?and=something&else=2457',
                    'QUERY_STRING' => 'and=something&else=2457',
                    ]), [
                        'and'=>'something',
                        'else'=>2457
                    ],[
                        'token' => 123,
                        'username' => 'test',
                        'password' => 'aaa'
                    ],
                    []
                ), 
                $RouterConfig, 
                ['controller'=>'\Everon\Test\MyController', 'action'=>'complex']],
        ];
    }

}