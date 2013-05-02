<?php
namespace Everon\Test;

class RouteItemTest extends \Everon\TestCase
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
        
        $RouteItem = new \Everon\RouteItem($data);
        
        $this->assertInstanceOf('\Everon\Interfaces\RouteItem', $RouteItem);
        $this->assertEquals($data['controller'], $RouteItem->getController());
        $this->assertEquals($data['action'], $RouteItem->getAction());
        $this->assertEquals($data['url'], $RouteItem->getUrl());
        $this->assertEquals($data['route_name'], $RouteItem->getName());
    }

}