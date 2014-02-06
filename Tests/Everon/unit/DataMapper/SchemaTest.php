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
use Everon\Helper;

class SchemaTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    public function testConstructor()
    {
        $ConnectionManager = $this->getMock('Everon\DataMapper\Interfaces\ConnectionManager');
        $Reader = $this->getMock('Everon\DataMapper\Interfaces\Schema\Reader');
        $Manager = new \Everon\DataMapper\Schema($Reader, $ConnectionManager);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Schema', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetConnectionManagerShouldReturnInstanceOfConnectionManager(DataMapper\Interfaces\Schema $Schema)
    {
        $ConnectionManager = $Schema->getConnectionManager();
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionManager', $ConnectionManager);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetPdoAdapterShouldReturnPdoAdapter(DataMapper\Interfaces\Schema $Schema)
    {
        $PdoAdapter = $Schema->getPdoAdapterByName('schema');
        $this->assertInstanceOf('Everon\Interfaces\PdoAdapter', $PdoAdapter);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnNameOfSchemaReader(DataMapper\Interfaces\Schema $Schema)
    {
        $name = $Schema->getDatabase();
        $this->assertEquals('everon_test', $name);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetTablesShouldReturnArray(DataMapper\Interfaces\Schema $Schema)
    {
        $tables = $Schema->getTables();
        $this->assertInternalType('array', $tables);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetTablesShouldSetTables(DataMapper\Interfaces\Schema $Schema)
    {
        $Schema->setTables([]);
        $this->assertInternalType('array', $Schema->getTables());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetTableShouldReturnSchemaTableInstance(DataMapper\Interfaces\Schema $Schema)
    {
        $Table = $Schema->getTable('user');
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Schema\Table', $Table);
    }
    
    public function dataProvider()
    {
        $tables = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['mysql_db_tables.php']);
        $columns = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['mysql_db_columns.php']);
        $primary_keys = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['mysql_db_primary_keys.php']);
        $foreign_keys = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['mysql_db_foreign_keys.php']);

        $PdoStub = new \Everon\Test\MyPdo();

        $ConnectionConfigMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionItem');
        $ConnectionConfigMock->expects($this->exactly(3))
            ->method('getDriver')
            ->will($this->returnValue('MySql'));

        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter');
        $PdoAdapterMock->expects($this->exactly(3))
            ->method('getConnectionConfig')
            ->will($this->returnValue($ConnectionConfigMock));

        $SchemaReaderMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Reader');
        $SchemaReaderMock->expects($this->once())
            ->method('getTableList')
            ->will($this->returnValue($tables));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getColumnList')
            ->will($this->returnValue($columns));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getPrimaryKeysList')
            ->will($this->returnValue($primary_keys));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getForeignKeyList')
            ->will($this->returnValue($foreign_keys));

        $SchemaReaderMock->expects($this->once())
            ->method('getDatabase')
            ->will($this->returnValue('everon_test'));

        $SchemaReaderMock->expects($this->exactly(3))
            ->method('getPdoAdapter')
            ->will($this->returnValue($PdoAdapterMock));
        
        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildPdo')
            ->will($this->returnValue($PdoStub));

        $FactoryMock->expects($this->once())
            ->method('buildPdoAdapter')
            ->will($this->returnValue($PdoAdapterMock));

        $SchemaTableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $FactoryMock->expects($this->exactly(3))
            ->method('buildSchemaTable')
            ->will($this->returnValue($SchemaTableMock));

        //$C = $this->getMock('Everon\DataMapper\Interfaces\ConnectionManager'); //why the fuck does it return null?
        $Factory = $this->buildFactory();
        $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getDatabaseConfig();
        $ConnectionManager = $Factory->buildConnectionManager($DatabaseConfig);
        
        $Schema = $Factory->buildSchema($SchemaReaderMock, $ConnectionManager);
        $Schema->setFactory($FactoryMock);
        
        return [
            [$Schema]
        ];
    }
}
