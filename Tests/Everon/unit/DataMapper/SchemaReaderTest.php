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

class SchemaReaderTest extends \Everon\TestCase
{
    use Helper\Arrays; 
    
    protected $fixtures = null;
    
    
    protected function setUpDumpSchema()
    {
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory( )->buildConnectionManager($DatabaseConfig);

        $Connection = $ConnectionManager->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
        $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
        $Reader = $this->getFactory()->buildSchemaReader($Connection, $PdoAdapter);
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\Schema\Reader', $Reader);
        $Reader->dumpDataBaseSchema($this->getDataMapperFixturesDirectory());
        die();
    }   
    
    public function SKIPtestConstructor()
    {
        $PdoAdapter = $this->getMock('\Everon\Interfaces\PdoAdapter');
        $Reader = new \Everon\DataMapper\Schema\MySql\Reader('everon_test', $PdoAdapter);
        $this->assertInstanceOf('\Everon\DataMapper\Interfaces\Schema\Reader', $Reader);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetTableListShouldReturnArrayWithTablesData(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['db_tables.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));
        
        $Reader->setPdoAdapter($PdoAdapterMock);
        $tables = $Reader->getTableList();

        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_tables.php']);
        
        $this->assertInternalType('array', $tables);
        $this->assertCount(3, $tables);
        $this->assertEquals($expected, $tables);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetColumnListShouldReturnArrayWithColumnsData(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['db_columns.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));
        
        $Reader->setPdoAdapter($PdoAdapterMock);
        $columns = $Reader->getColumnList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_columns.php']);
        
        $this->assertInternalType('array', $columns);
        $this->assertCount(3, $columns);
        $this->assertEquals($expected, $columns);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetConstraintListShouldReturnArrayWithColumnsData(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['db_constraints.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));
        
        $Reader->setPdoAdapter($PdoAdapterMock);
        $constraints = $Reader->getColumnList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_constraints.php']);
        
        $this->assertInternalType('array', $constraints);
        $this->assertCount(3, $constraints);
        $this->assertEquals($expected, $constraints);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetForeignKeyListListShouldReturnArrayWithColumnsData(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['db_foreign_keys.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $foreign_keys = $Reader->getColumnList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_foreign_keys.php']);

        $this->assertInternalType('array', $foreign_keys);
        $this->assertCount(1, $foreign_keys);
        $this->assertEquals($expected, $foreign_keys);
    }
    
    public function dataProvider()
    {
        $ConnectionItem = $this->getMock('\Everon\DataMapper\Interfaces\ConnectionItem');
        $ConnectionItem->expects($this->once())
            ->method('getAdapterName')
            ->will($this->returnValue('MySql'));
        $ConnectionItem->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('everon_test'));
        
        $PdoAdapterMock = $this->getMock('\Everon\Interfaces\PdoAdapter');
        $Reader = $this->getFactory()->buildSchemaReader($ConnectionItem, $PdoAdapterMock);
        
        return [
            [$Reader, $PdoAdapterMock]
        ];
    }
}
  