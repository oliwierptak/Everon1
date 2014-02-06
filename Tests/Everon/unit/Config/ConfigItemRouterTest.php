<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Config;

class ItemRouterTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $data = [
            '____name' => 'test',
            'url' => '/',
            'controller' => 'Test',
            'action' => 'testMe',
            'get' => [],
            'post' => [],
            'default' => true,
        ];
        
        $Item = new \Everon\Config\Item\Router($data);
        
        $this->assertInstanceOf('Everon\Interfaces\ConfigItemRouter', $Item);
        $this->assertEquals($data['controller'], $Item->getController());
        $this->assertEquals($data['action'], $Item->getAction());
        $this->assertEquals($data['url'], $Item->getUrl());
        $this->assertEquals($data['____name'], $Item->getName());
    }

}