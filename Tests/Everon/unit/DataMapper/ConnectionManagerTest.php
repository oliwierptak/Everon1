<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper;

use Everon\DataMapper\Connection;
use Everon\Interfaces;

class ConnectionManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $connections = [
            'test' => []
        ];
        $Manager = new \Everon\DataMapper\Connection\Manager($connections);
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\ConnectionManager', $Manager);
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testAddShouldAddNewConnection(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertCount(2, $Manager->getConnections());
        
        $Item = $this->getMock('\Everon\DataMapper\Interfaces\ConnectionItem');
        $Manager->add('test', $Item);
        
        $this->assertCount(3, $Manager->getConnections());
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\ConnectionItem', $Manager->getConnectionByName('test'));
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\ConnectionItem', $Manager->getConnections()['test']);
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testRemoveShouldRemoveExistingConnection(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertCount(2, $Manager->getConnections());

        $Manager->remove('example');

        $this->assertCount(1, $Manager->getConnections());
    }
    
    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testGetConnectionsShouldReturnArrayWithConnections(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertInternalType('array', $Manager->getConnections());
        $this->assertCount(2, $Manager->getConnections());
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testGetConnectionByNameShouldReturnInstanceOfConnectionItem(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertInternalType('array', $Manager->getConnections());
        $this->assertCount(2, $Manager->getConnections());
        
        $Item = $Manager->getConnectionByName('example');
        
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\ConnectionItem', $Item);
    }

    public function dataProvider()
    {
        /**
         * @var Interfaces\Config $DatabaseConfig
         */
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory()->buildConnectionManager($DatabaseConfig);
        
        return [
            [$ConnectionManager, $DatabaseConfig]
        ];
    }

}
