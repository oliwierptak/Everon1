<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper\PostgreSql;

use Everon\DataMapper;
use Everon\Interfaces;
use Everon\Helper;

class SchemaReaderTest extends \Everon\TestCase
{
    use Helper\Arrays; 
    
    protected $fixtures = null;
    
    
    protected function setUpDumpSchema()
    {
        $driver = 'pgsql';
        $Factory = $this->buildFactory();
        $DatabaseConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database_'.$driver);
        $ConnectionManager = $Factory->buildConnectionManager($DatabaseConfig);

        $Connection = $ConnectionManager->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $Pdo = $Factory->buildPdo($dsn, $username, $password, $options);
        $PdoAdapter = $Factory->buildPdoAdapter($Pdo, $Connection);
        $Reader = $Factory->buildSchemaReader($PdoAdapter);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Schema\Reader', $Reader);
        $Reader->TMPdumpDataBaseSchema($this->getDataMapperFixturesDirectory(), $driver);
        die();
    }   
    
    public function testConstructor()
    {
        $PdoAdapter = $this->getMock('Everon\Interfaces\PdoAdapter');
        $Reader = new \Everon\DataMapper\Schema\PostgreSql\Reader($PdoAdapter);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Schema\Reader', $Reader);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetTableListShouldReturnArrayWithTablesData(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['pgsql_db_tables.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $tables = $Reader->getTableList();

        $expected = $this->arrayArrangeByKeySingle('TABLE_NAME', $this->getFixtureData()['pgsql_db_tables.php']);
        
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
            ->will($this->returnValue($this->getFixtureData()['pgsql_db_columns.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $columns = $Reader->getColumnList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['pgsql_db_columns.php']);

        $this->assertInternalType('array', $columns);
        $this->assertCount(3, $columns);
        $this->assertEquals($expected, $columns);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetPrimaryKeysListShouldReturnArrayWithPrimaryKeys(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['pgsql_db_primary_keys.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $primary_keys = $Reader->getPrimaryKeysList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['pgsql_db_primary_keys.php']);

        $this->assertInternalType('array', $primary_keys);
        $this->assertCount(3, $primary_keys);
        $this->assertEquals($expected, $primary_keys);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetForeignKeyListListShouldReturnArrayForeignKeys(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['pgsql_db_foreign_keys.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $foreign_keys = $Reader->getForeignKeyList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['pgsql_db_foreign_keys.php']);

        $this->assertInternalType('array', $foreign_keys);
        $this->assertCount(1, $foreign_keys);
        $this->assertEquals($expected, $foreign_keys);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testUniqueKeyListShouldReturnArrayUniqueKeys(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $PdoStatementMock = $this->getMock('\PDOStatement');
        $PdoStatementMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($this->getFixtureData()['pgsql_db_unique_keys.php']));

        $PdoAdapterMock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($PdoStatementMock));

        $Reader->setPdoAdapter($PdoAdapterMock);
        $foreign_keys = $Reader->getUniqueKeysList();
        $expected = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['pgsql_db_unique_keys.php']);

        $this->assertInternalType('array', $foreign_keys);
        $this->assertCount(1, $foreign_keys);
        $this->assertEquals($expected, $foreign_keys);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetDriverShouldReturnDriverName(\Everon\DataMapper\Interfaces\Schema\Reader $Reader, \Everon\Interfaces\PdoAdapter $PdoAdapterMock)
    {
        $ConnectionConfigMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionItem');
        $PdoAdapterMock->expects($this->once())
            ->method('getConnectionConfig')
            ->will($this->returnValue($ConnectionConfigMock));

        $Reader->setPdoAdapter($PdoAdapterMock);

        $this->assertEquals('pgsql', $Reader->getDriver());
    }


    public function dataProvider()
    {
        $ConnectionItemMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionItem');
        $ConnectionItemMock->expects($this->any())
            ->method('getAdapterName')
            ->will($this->returnValue('MySql'));
        $ConnectionItemMock->expects($this->any())
            ->method('getDatabase')
            ->will($this->returnValue('everon_test'));
        $ConnectionItemMock->expects($this->once())
            ->method('getDriver')
            ->will($this->returnValue('pgsql'));
        
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter');
        $PdoAdapterMock->expects($this->any())
            ->method('getConnectionConfig')
            ->will($this->returnValue($ConnectionItemMock));
        
        $Reader = $this->buildFactory()->buildSchemaReader($PdoAdapterMock);
        
        return [
            [$Reader, $PdoAdapterMock]
        ];
    }
}
  