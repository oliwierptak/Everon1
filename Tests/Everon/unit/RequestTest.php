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

class RequestTest extends \Everon\TestCase
{
    protected static $pass = 0;
    
    public function setUp()
    {
        ++static::$pass;
    }
    
    public function testConstructor()
    {
        $Request = new \Everon\Request([
            'SERVER_PROTOCOL'=> 'HTTP/1.1',
            'REQUEST_METHOD'=> 'GET',
            'REQUEST_URI'=> '/',
            'QUERY_STRING'=> '?foo=bar',
            'SERVER_NAME'=> 'everon.nova',
            'SERVER_PORT'=> 80,
            'SERVER_ADDR'=> '127.0.0.1',
            'REMOTE_ADDR'=> '127.0.0.1',
            'HTTPS'=> 'off',
        ],[
            'foo' => 'bar'
        ],[],[]);
        
        $this->assertInstanceOf('\Everon\Interfaces\Request', $Request);
        $this->assertInternalType('array', $Request->getGetCollection());
        $this->assertInternalType('array', $Request->getPostCollection());
        $this->assertInternalType('array', $Request->getFileCollection());        
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Interfaces\Request $Request, array $expected)
    {
        $this->assertEquals($expected['method'], $Request->getMethod());
        $this->assertEquals($expected['url'], $Request->getUrl());
        $this->assertEquals($expected['location'], $Request->getLocation());
        $this->assertEquals($expected['query_string'], $Request->getQueryString());
        $this->assertEquals($expected['path'], $Request->getPath());
        $this->assertEquals($expected['port'], $Request->getPort());
        $this->assertEquals($expected['protocol'], $Request->getProtocol());
        $this->assertFalse($Request->isSecure());

        $Request->setMethod($expected['method']);
        $Request->setUrl($expected['url']);
        $Request->setQueryString($expected['query_string']);
        $Request->setPath($expected['path']);
        $Request->setPort($expected['port']);
        $Request->setProtocol($expected['protocol']);

        $this->assertEquals($expected['method'], $Request->getMethod());
        $this->assertEquals($expected['url'], $Request->getUrl());
        $this->assertEquals($expected['query_string'], $Request->getQueryString());
        $this->assertEquals($expected['path'], $Request->getPath());
        $this->assertEquals($expected['port'], $Request->getPort());
        $this->assertEquals($expected['protocol'], $Request->getProtocol());
        $this->assertFalse($Request->isSecure());
        
        $Request->setPostCollection($Request->getPostCollection());
        $Request->setGetCollection($Request->getGetCollection());
        $Request->setFileCollection($Request->getFileCollection());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testIsSecure(\Everon\Interfaces\Request $Request, array $expected)
    {
        $this->assertFalse($Request->isSecure());
        
        $Server = $Request->getServerCollection();
        $Server['HTTPS'] = 'on';
        $Server['SERVER_PORT'] = 443;
        $Request->setServerCollection($Server);
        $this->assertTrue($Request->isSecure());

        $Server = $Request->getServerCollection();
        unset($Server['HTTPS']);
        $Server['SSL_HTTPS'] = 'on';
        $Request->setServerCollection($Server);
        $this->assertTrue($Request->isSecure());

        $Server = $Request->getServerCollection();
        unset($Server['HTTPS']);
        unset($Server['SSL_HTTPS']);
        $Server['SERVER_PORT'] = 443;
        $Request->setServerCollection($Server);
        $this->assertTrue($Request->isSecure());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Request
     * @expectedExceptionMessage Missing required parameter: "location"
     */
    public function testValidateShouldThrowExceptionWhenWrongData(\Everon\Interfaces\Request $Request, array $expected)
    {
        $method = $this->getProtectedMethod('\Everon\Request', 'validate');
        $this->assertEquals('', $method->invoke($Request, []));
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Request
     * @expectedExceptionMessage Unrecognized http method: "wrong"
     */
    public function testValidateShouldThrowExceptionWhenWrongMethod(\Everon\Interfaces\Request $Request, array $expected)
    {
        $method = $this->getProtectedMethod('\Everon\Request', 'validate');
        $this->assertEquals('', $method->invoke($Request, [
            'location' => 'wrong',
            'method'=>'wrong',
            'url'=>'/',
            'query_string'=> '',
            'path' => '',
            'protocol' => '',
            'port' => '',
            'secure' => ''            
        ]));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetSetParameters(\Everon\Interfaces\Request $Request, array $expected)
    {
        if (static::$pass === 1) {
            $this->assertEquals($expected['url'], $Request->getUrl());
            $this->assertEquals($expected['post'], $Request->getPostCollection());
            $this->assertEquals($expected['post']['login'], $Request->getPostParameter('login'));
            $this->assertEquals($expected['post']['password'], $Request->getPostParameter('password'));
        }
        
        if (static::$pass === 2) {
            $this->assertEquals($expected['url'], $Request->getUrl());
            $this->assertEquals($expected['get'], $Request->getGetCollection());
            $this->assertEquals($expected['get']['param1'], $Request->getQueryParameter('param1'));
            $this->assertEquals($expected['get']['param2'], $Request->getQueryParameter('param2'));
        }

        $this->assertEquals(null, $Request->getQueryParameter('wrong one'));
        $this->assertEquals(null, $Request->getPostParameter('wrong one'));

        $Request->setQueryParameter('test', 1);
        $this->assertEquals(1, $Request->getQueryParameter('test'));
        
        $Request->setPostParameter('test', 2);
        $this->assertEquals(2, $Request->getPostParameter('test'));        
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSanitizeInput(\Everon\Interfaces\Request $Request, array $expected)
    {
        $Server = $Request->getServerCollection();
        $Server['REQUEST_URI'] = '<?php //this is wrong; ?>';
        $files = ['test'=> [
            'true' => '<?php phpinfo(); ?>',
            'something' => '//@sadfasd ',
        ]];

        $Request->setFileCollection($files);
        $files = $Request->getFileCollection();
        $this->assertInternalType('array', $files);

        $method = $this->getProtectedMethod('\Everon\Request', 'sanitizeInput');
        $this->assertEquals('', $method->invoke($Request, '<?php //test ;?>'));

        $this->assertEquals('', $files['test']['true']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetHostNameFromGlobals(\Everon\Interfaces\Request $Request, array $expected)
    {
        $Server = $Request->getServerCollection();
        $Server['HTTP_HOST'] = $Server['SERVER_NAME'];
        unset($Server['SERVER_NAME']);
        $Request->setServerCollection($Server);
        $this->assertEquals($expected['path'], $Request->getPath());

        $Server = $Request->getServerCollection();
        unset($Server['HTTP_HOST']);
        unset($Server['SERVER_NAME']);
        $Request->setServerCollection($Server);
        $this->assertEquals($expected['location_fallback'], $Request->getLocation());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToArray(\Everon\Interfaces\Request $Request, array $expected)
    {
        $this->assertInternalType('array', $Request->toArray());
    }

    public function dataProvider()
    {        
        return [[
            new \Everon\Request($this->getServerDataForRequest([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/login',
                'QUERY_STRING' => '']),
                [],
                ['login' => 'test',
                    'password' => 'test'],
                []),
            //expected
            [
                'location' => 'http://everon.nova',
                'location_fallback' => 'http://127.0.0.1',
                'url' => 'http://everon.nova/login',
                'path' => '/login',
                'method' => 'POST',
                'query_string' => '',
                'port' => 80,
                'protocol' => 'HTTP/1.1',
                'get' => [],
                'post' => ['login' => 'test', 'password' => 'test'],
                'files' => []
            ]
        ],[
            new \Everon\Request($this->getServerDataForRequest([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/search?param1=val1&param2=val2',
                'QUERY_STRING' => 'param1=val1&param2=val2']),
                ['param1' => 'val1',
                    'param2' => 'val2'],
                [],
                []),
            //expected
            [
                'location' => 'http://everon.nova',
                'location_fallback' => 'http://127.0.0.1',
                'url' => 'http://everon.nova/search?param1=val1&param2=val2',
                'path' => '/search?param1=val1&param2=val2',
                'method' => 'GET',
                'query_string' => 'param1=val1&param2=val2',                
                'port' => 80,
                'protocol' => 'HTTP/1.1',
                'get' => ['param1' => 'val1', 'param2' => 'val2'],
                'post' => [],
                'files' => []
            ]
        ]];
    }

}