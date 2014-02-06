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
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionManager', $Manager);
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testAddShouldAddNewConnection(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertCount(4, $Manager->getConnections());
        
        $Item = $this->getMock('Everon\DataMapper\Interfaces\ConnectionItem');
        $Manager->add('test', $Item);
        
        $this->assertCount(5, $Manager->getConnections());
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionItem', $Manager->getConnectionByName('test'));
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionItem', $Manager->getConnections()['test']);
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testRemoveShouldRemoveExistingConnection(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertCount(4, $Manager->getConnections());

        $Manager->remove('schema');

        $this->assertCount(3, $Manager->getConnections());
    }
    
    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testGetConnectionsShouldReturnArrayWithConnections(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertInternalType('array', $Manager->getConnections());
        $this->assertCount(4, $Manager->getConnections());
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     */
    public function testGetConnectionByNameShouldReturnInstanceOfConnectionItem(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertInternalType('array', $Manager->getConnections());
        $this->assertCount(4, $Manager->getConnections());
        
        $Item = $Manager->getConnectionByName('schema');
        
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionItem', $Item);
    }

    /**
     * @dataProvider dataProvider
     * @param Connection\Manager $Manager
     * @param Interfaces\Config $DataBaseConfig
     * @expectedException \Everon\DataMapper\Exception\ConnectionManager
     * @expectedExceptionMessage Invalid connection name: "i dont exist"
     */
    public function testGetConnectionByNameShouldThrowExceptionWhenWrongName(Connection\Manager $Manager, Interfaces\Config $DataBaseConfig)
    {
        $this->assertInternalType('array', $Manager->getConnections());
        $this->assertCount(4, $Manager->getConnections());

        $Item = $Manager->getConnectionByName('i dont exist');
    }

    public function dataProvider()
    {
        /**
         * @var Interfaces\Config $DatabaseConfig
         */

        $Factory = $this->buildFactory();
        $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getDatabaseConfig();
        $ConnectionManager = $Factory->buildConnectionManager($DatabaseConfig);
        
        return [
            [$ConnectionManager, $DatabaseConfig]
        ];
    }

}
