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

use Everon\DataMapper;
use Everon\Interfaces;

class SchemaTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $ConnectionManager = $this->getMock('\Everon\DataMapper\Interfaces\ConnectionManager');
        $Reader = $this->getMock('\Everon\DataMapper\Interfaces\Schema\Reader');
        $Reader->expects($this->once())
            ->method('getTableList')
            ->will($this->returnValue([]));
        
        $Manager = new \Everon\DataMapper\Schema($Reader, $ConnectionManager);
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\Schema', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetPdoAdapterShouldReturnPdoAdapter(DataMapper\Interfaces\Schema $Schema)
    {
        $PdoAdapter = $Schema->getPdoAdapter('schema');
        $this->assertInstanceOf('\Everon\Interfaces\PdoAdapter', $PdoAdapter);
    }

    public function dataProvider()
    {
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory()->buildConnectionManager($DatabaseConfig);
        
        $Connection = $ConnectionManager->getConnectionByName('schema');
        $PdoMock =  $this->getMock('\Everon\Test\MyPdo');
        $PdoAdapter = $this->getFactory()->buildPdoAdapter($PdoMock, $Connection);
        $SchemaReaderMock = $this->getMock('\Everon\DataMapper\Interfaces\Schema\Reader');     
            
        $SchemaReaderMock->expects($this->once())
            ->method('getTableList')
            ->will($this->returnValue([]));

        $FactoryMock = $this->getMock('\Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildPdo')
            ->will($this->returnValue($PdoMock));

        $FactoryMock->expects($this->once())
            ->method('buildPdoAdapter')
            ->will($this->returnValue($PdoAdapter));
        
        $Schema = $this->getFactory()->buildSchema($SchemaReaderMock, $ConnectionManager);
        $Schema->setFactory($FactoryMock);
        
        return [
            [$Schema]
        ];
    }

}
