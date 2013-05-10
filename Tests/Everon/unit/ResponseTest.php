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
    public function AtestToJson(\Everon\Interfaces\Factory $Factory)
    {
        $this->markTestSkipped();
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
    public function AtestToHtml(\Everon\Interfaces\Factory $Factory)
    {
        $this->markTestSkipped();
        $Response = $Factory->buildResponse(['test'=>'yes']);
        $this->assertInternalType('string', $Response->toHtml());

        $Response->send();

        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'content-type: text/html; charset="utf-8"');        
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\DependencyContainer $Container
         * @var \Everon\Interfaces\Factory $Factory
         */
        list($Container, $Factory) = $this->getContainerAndFactory();        
        
        return [
            [$Factory]
        ];
    }

}