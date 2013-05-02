<?php
namespace Everon\Test;

class ResponseTest extends \Everon\TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testConstructor(\Everon\Interfaces\Factory $Factory)
    {
        $Response = $Factory->buildResponse(['test'=>'yes']);
        $this->assertInstanceOf('\Everon\Interfaces\Response', $Response);
    }

    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testToJson(\Everon\Interfaces\Factory $Factory)
    {
        $Response = $Factory->buildResponse(['test'=>'yes']);
        $this->assertInternalType('string', $Response->toJson());
        $this->assertEquals('{"data":{"test":"yes"}}', $Response->toJson());
        
        $Response->send();
        
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'content-type: application/json');
    }
    
    /**
     * @dataProvider dataProvider
     * @runInSeparateProcess
     */
    public function testToHtml(\Everon\Interfaces\Factory $Factory)
    {
        $Response = $Factory->buildResponse(['test'=>'yes']);
        $this->assertInternalType('string', $Response->toHtml());

        $Response->send();

        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'content-type: text/html; charset="utf-8"');        
    }
    
    public function dataProvider()
    {
        $dc = new \Everon\Dependency\Container();
        $MyFactory = new \Everon\Test\MyFactory($dc);
        
        return [
            [$MyFactory]
        ];
    }

}