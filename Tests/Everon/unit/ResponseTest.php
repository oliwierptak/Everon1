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

class ResponseTest extends \Everon\TestCase
{
    
    public function testConstructor()
    {
        $HeadersMock = $this->getMock('Everon\Http\HeaderCollection');
        $Response = new \Everon\Response('Guid', $HeadersMock);
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
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        
        return [
            [$Factory]
        ];
    }

}