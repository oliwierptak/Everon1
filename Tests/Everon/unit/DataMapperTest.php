<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test;

use Everon\Domain;
use Everon\Domain\Interfaces;
use Everon\Helper;

class DataMapperTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    public function testConstructor()
    {
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [], [],'', false);
        $DataMapper = new DataMapper\MySql\User($TableMock, $SchemaMock);
        $this->assertInstanceOf('\Everon\Interfaces\DataMapper', $DataMapper);
    }

    /**
     * @22dataProvider dataProvider
     */
    public function SKIPtestWithRealDatabase()
    {
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory( )->buildConnectionManager($DatabaseConfig);

        $Connection = $ConnectionManager->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
        $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
        $Reader = $this->getFactory()->buildSchemaReader($Connection, $PdoAdapter);
        $Schema = $this->getFactory()->buildSchema($Reader, $ConnectionManager);
        $Table = $Schema->getTable('user');
        $Mapper = $this->getFactory()->buildDataMapper($Table, $Schema);
        
        //sd($Mapper);
        
        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
  
        $Entity = new \Everon\Test\Domain\User\Entity(null, $entity_data);
        $this->assertTrue($Entity->isNew());
        
        $id = $Mapper->add($Entity);
        $this->assertInstanceOf('\Everon\Test\Domain\User\Entity', $Entity);
        
        
        $data = $Entity->toArray();
        $Entity->reload($id, $data);
        $Mapper->save($Entity);
        
        $FetchedEntity = $Mapper->fetchOne($id);
        $this->assertInternalType('array', $FetchedEntity);

        $Mapper->delete($Entity);
        
        $Criteria = new \Everon\DataMapper\Criteria();
        $all = $Mapper->fetchAll($Criteria);
        $this->assertInternalType('array', $all);
        $this->assertCount(10, $all);
    } 
    
    /**
     * @dataProvider dataProvider
     */
    public function AAtestAddShouldInsertEntity(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapterMock)
    {
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $ConnectionManager = $this->getFactory( )->buildConnectionManager($DatabaseConfig);

        $Connection = $ConnectionManager->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
        $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
        $Reader = $this->getFactory()->buildSchemaReader($Connection, $PdoAdapter);
        $Schema = $this->getFactory()->buildSchema($Reader, $ConnectionManager);
        $Table = $Schema->getTable('user');
        $Mapper = $this->getFactory()->buildDataMapper($Table, $Schema);
        
        //sd($Mapper);
        
        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $PdoAdapterMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue($entity_data));
        
        $Entity = new \Everon\Test\Domain\User\Entity(null, $entity_data);
        $Mapper->add($Entity);
        
        $this->assertInstanceOf('\Everon\Test\Domain\User\Entity', $Entity);
        $this->assertEquals(123, $Entity->getId());
        $this->assertTrue($Entity->isPersisted());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function AAtestSaveShouldSaveEntity(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapterMock)
    {
        $entity_data = [
            'id' => 123,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $PdoAdapterMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue($entity_data));
        
        $Entity = new \Everon\Test\Domain\User\Entity(1, $entity_data);
        $Mapper->save($Entity);
        
        $this->assertInstanceOf('\Everon\Test\Domain\User\Entity', $Entity);
        $this->assertEquals(1, $Entity->getId());
        $this->assertTrue($Entity->isPersisted());
    }
  
    public function dataProvider()
    {
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter', [], [], '', false);

        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema', [], [], '', false);
        $SchemaMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('everon_test'));
        $SchemaMock->expects($this->once())
            ->method('getDriver')
            ->will($this->returnValue('MySql'));

        $SchemaMock->expects($this->once())
            ->method('getPdoAdapterByName')
            ->will($this->returnValue($PdoAdapterMock));
       
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [],[], '', false);
        $TableMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('user'));
        
        $columns = $this->arrayArrangeByKey('TABLE_NAME', $this->getFixtureData()['db_columns.php']);
        $Table = $this->getFactory()->buildSchemaTable('user', 'MySql', $columns['user'], [], []);
        $Mapper = $this->getFactory()->buildDataMapper($Table, $SchemaMock, 'Everon\Test\DataMapper');
        
        return [
            [$Mapper, $PdoAdapterMock]
        ];
    }
}