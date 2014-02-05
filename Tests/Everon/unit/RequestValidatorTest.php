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

class RequestValidatorTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $Validator = new \Everon\RequestValidator();
        $this->assertInstanceOf('\Everon\Interfaces\RequestValidator', $Validator);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testValidate(Interfaces\RequestValidator $Validator, Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $result = $Validator->validate($RouteItem, $Request);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\InvalidRoute
     * @expectedExceptionMessage Invalid required parameter: "password" for route: "test_complex"
     */
    public function testValidateShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $post = $Request->getPostCollection();
        $post['password'] = '';
        $Request->setPostCollection($post);
        
        $result = $Validator->validate($RouteItem, $Request);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\RequestValidator
     */
    public function testValidateQueryShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $RouteItemMock = $this->getMockBuilder('Everon\Config\Item\Router')
            ->disableOriginalConstructor()
            ->getMock();
        
        $RouteItemMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('anitem'));
        
        $RouteItemMock->expects($this->once())
            ->method('getCleanUrl')
            ->will($this->throwException(new \Exception('exception')));
        
        $result = $Validator->validate($RouteItemMock, $Request);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\RequestValidator
     */
    public function testValidatePostShouldThrowExceptionWhenError(Interfaces\RequestValidator $Validator, Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request)
    {
        $RouteItemMock = $this->getMock('Everon\Config\Item\Router', [], [], '', false);
        $RouteItemMock->expects($this->once())
            ->method('filterQueryKeys')
            ->will($this->returnValue([]));
        
        $RouteItemMock->expects($this->any())
            ->method('getPostRegex')
            ->will($this->throwException(new \Exception('getPostRegex')));
        
        $result = $Validator->validate($RouteItemMock, $Request);
        $this->assertInternalType('array', $result);
    }
    
    public function dataProvider()
    {
        $Factory = $this->buildFactory();

        $server_data = $this->getServerDataForRequest([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/login/submit/session/adf24ds34/redirect/account%5Csummary?and=something&else=2457',
            'QUERY_STRING' => 'and=something&else=2457',
        ]);
        $Request = $Factory->buildRequest(
            $server_data, [
                'and'=>'something',
                'else'=>2457
            ],[
                'token' => 3,
                'username' => 'test',
                'password' => 'aaa'
            ],
            []
        );

        $Config = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('router');
        $RouteItem = $Config->getItemByName('test_complex');
        $Validator = new \Everon\RequestValidator();
        
        return [
            [$Validator, $RouteItem, $Request]
        ];
    }

}
