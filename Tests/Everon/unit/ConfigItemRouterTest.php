<?php
namespace Everon\Test;

class ConfigItemRouterTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $data = [
            'route_name' => 'test',
            'url' => '/',
            'controller' => 'Test',
            'action' => 'testMe',
            'get' => [],
            'post' => [],
            'default' => true,
        ];
        
        $Item = new \Everon\Config\Item\Router($data);
        
        $this->assertInstanceOf('\Everon\Interfaces\ConfigItemRouter', $Item);
        $this->assertEquals($data['controller'], $Item->getController());
        $this->assertEquals($data['action'], $Item->getAction());
        $this->assertEquals($data['url'], $Item->getUrl());
        $this->assertEquals($data['route_name'], $Item->getName());
    }

}