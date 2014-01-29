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
        $ConnectionManager = $this->getMock('\Everon\DataMapper\Interfaces\ConnectionManager');
        $Reader = $this->getMock('\Everon\DataMapper\Interfaces\Schema\Reader');
        
        $Manager = new \Everon\DataMapper\Schema($Reader, $ConnectionManager);
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\Schema', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetConnectionManagerShouldReturnInstanceOfConnectionManager(DataMapper\Interfaces\Schema $Schema)
    {
        $ConnectionManager = $Schema->getConnectionManager();
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\ConnectionManager', $ConnectionManager);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetPdoAdapterShouldReturnPdoAdapter(DataMapper\Interfaces\Schema $Schema)
    {
        $PdoAdapter = $Schema->getPdoAdapterByName('schema');
        $this->assertInstanceOf('\Everon\Interfaces\PdoAdapter', $PdoAdapter);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnNameOfSchemaReader(DataMapper\Interfaces\Schema $Schema)
    {
        $name = $Schema->getName();
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
        $Table = $Schema->getTable('test_user');
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\Schema\Table', $Table);
    }
    
    public function dataProvider()
    {
        $SchemaReaderMock = $this->getMock('\Everon\DataMapper\Interfaces\Schema\Reader');
        
        $tables = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_tables.php']);
        $columns = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_columns.php']);
        $constraints = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_constraints.php']);
        $foreign_keys = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_foreign_keys.php']);

        $SchemaReaderMock->expects($this->once())
            ->method('getTableList')
            ->will($this->returnValue($tables));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getColumnList')
            ->will($this->returnValue($columns));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getConstraintList')
            ->will($this->returnValue($constraints));
        
        $SchemaReaderMock->expects($this->once())
            ->method('getForeignKeyList')
            ->will($this->returnValue($foreign_keys));

        $SchemaReaderMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('everon_test'));

        //$C = $this->getMock('\Everon\DataMapper\Interfaces\ConnectionManager'); //why the fuck does it return null
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory()->buildConnectionManager($DatabaseConfig);
        
        $Schema = $this->getFactory()->buildSchema($SchemaReaderMock, $ConnectionManager);
        
        return [
            [$Schema]
        ];
    }
}
