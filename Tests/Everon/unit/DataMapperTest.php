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
        
        $fetched_data = $Mapper->fetchOne($id);
        $this->assertInternalType('array', $fetched_data);

        $Mapper->delete($Entity);
        $this->assertInstanceOf('\Everon\Domain\Interfaces\Entity', $Entity);
        $this->assertNull($Entity->getId());
        
        $Criteria = new \Everon\DataMapper\Criteria();
        $all = $Mapper->fetchAll($Criteria);
        $this->assertInternalType('array', $all);
        $this->assertCount(10, $all);
    } 
    
    /**
     * @dataProvider dataProvider
     */
    public function testAddShouldInsertEntity(\Everon\Interfaces\DataMapper $Mapper, $PdoAdapterMock)
    {
        $entity_data = [
            'id' => null,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        $PdoAdapterMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue($entity_data));
        
        $PdoAdapterMock->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        
        $Entity = new \Everon\Test\Domain\User\Entity(null, $entity_data);
        $id = $Mapper->add($Entity);
        
        $this->assertInstanceOf('\Everon\Test\Domain\User\Entity', $Entity);
        $this->assertEquals(null, $Entity->getId());
        $this->assertEquals(1, $id);
        $this->assertTrue($Entity->isNew()); //repository should set the states
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
        
        $ColumnMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $ColumnMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('id'));
        
        $columns = [];
        $columns['user'] = $ColumnMock;

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
        $SchemaMock->expects($this->once())
            ->method('getTableList')
            ->will($this->returnValue([$ColumnMock]));

        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [],[], '', false);
        $TableMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('user'));
        $TableMock->expects($this->once())
            ->method('validateId')
            ->will($this->returnValue(1));
        $TableMock->expects($this->any())
            ->method('getColumns')
            ->will($this->returnValue([$ColumnMock]));
/*
        $PrimaryKeyMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\PrimaryKey');
        $PrimaryKeyMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('id'));


        

        $TableMock->expects($this->once())
            ->method('validateId')
            ->will($this->returnValue(1));
        $TableMock->expects($this->once())
            ->method('getPrimaryKeys')
            ->will($this->returnValue([$PrimaryKeyMock]));*/


        //$Table = $this->getFactory()->buildSchemaTable('user', 'MySql', $columns['user'], [], []);
        $Mapper = $this->getFactory()->buildDataMapper($TableMock, $SchemaMock, 'Everon\Test\DataMapper');
        
        return [
            [$Mapper, $PdoAdapterMock]
        ];
    }
}