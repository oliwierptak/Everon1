<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Domain;

use Everon\Interfaces;

class ManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $ConnectionManagerMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionManager');
        $Manager = new \Everon\Test\Domain\Manager($ConnectionManagerMock);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Handler', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetEntityShouldReturnEntity(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $EntityMock = $this->getMock('Everon\Domain\Interfaces\Entity');
        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDomainEntity')
            ->will($this->returnValue($EntityMock));
        
        $DomainManager->setFactory($FactoryMock);
        $Entity = $DomainManager->getEntity('User', 1, []);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $Entity);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetModelShouldReturnModel(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $ModelMock = $this->getMock('Everon\Test\Domain\User\Model');
        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDomainModel')
            ->will($this->returnValue($ModelMock));

        $DomainManager->setFactory($FactoryMock);
        $Model = $DomainManager->getModel('User');
        
        $this->assertInstanceOf('Everon\Test\Domain\User\Model', $Model);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetRepositoryShouldReturnRepository(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $PdoMock = $this->getMock('Everon\Test\MyPdo');
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter');
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $SchemaTableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $SchemaReaderMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Reader');
        
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $SchemaMock->expects($this->once())
            ->method('getTable')
            ->will($this->returnValue($SchemaTableMock));
        
        $RepositoryMock = $this->getMock('Everon\Test\Domain\User\Repository', [], [], '', false);
        
        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildDataMapper')
            ->will($this->returnValue($DataMapperMock));
        $FactoryMock->expects($this->once())
            ->method('buildDomainRepository')
            ->will($this->returnValue($RepositoryMock));
        $FactoryMock->expects($this->once())
            ->method('buildPdo')
            ->will($this->returnValue($PdoMock));
        $FactoryMock->expects($this->once())
            ->method('buildPdoAdapter')
            ->will($this->returnValue($PdoAdapterMock));
        $FactoryMock->expects($this->once())
            ->method('buildSchemaReader')
            ->will($this->returnValue($SchemaReaderMock));
        $FactoryMock->expects($this->once())
            ->method('buildSchema')
            ->will($this->returnValue($SchemaMock));

        $DomainManager->setFactory($FactoryMock);
        $Repository = $DomainManager->getRepository('User');
        
        $this->assertInstanceOf('Everon\Test\Domain\User\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetSchemaShouldReturnSchema(\Everon\Domain\Interfaces\Manager $DomainManager)
    {
        $PdoMock = $this->getMock('Everon\Test\MyPdo');
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter');
        $SchemaReaderMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Reader');
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');

        $FactoryMock = $this->getMock('Everon\Interfaces\Factory');
        $FactoryMock->expects($this->once())
            ->method('buildPdo')
            ->will($this->returnValue($PdoMock));
        $FactoryMock->expects($this->once())
            ->method('buildPdoAdapter')
            ->will($this->returnValue($PdoAdapterMock));
        $FactoryMock->expects($this->once())
            ->method('buildSchemaReader')
            ->will($this->returnValue($SchemaReaderMock));
        $FactoryMock->expects($this->once())
            ->method('buildSchema')
            ->will($this->returnValue($SchemaMock));

        $DomainManager->setFactory($FactoryMock);
        $Schema = $DomainManager->getSchema('everon_test');

        $this->assertInstanceOf('Everon\DataMapper\Interfaces\Schema', $Schema);
    }
    
    public function dataProvider()
    {
        /**
         * @var \Everon\DataMapper\Interfaces\ConnectionManager $ConnectionManagerMock
         */
        $ConnectionMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionItem');
        $ConnectionMock->expects($this->once())
            ->method('toPdo')
            ->will($this->returnValue(['dsn','user','password','options']));
        
        $ConnectionManagerMock = $this->getMock('Everon\DataMapper\Interfaces\ConnectionManager');
        $ConnectionManagerMock->expects($this->once())
            ->method('getConnectionByName')
            ->with('schema')
            ->will($this->returnValue($ConnectionMock));
        
        $DomainManager = $this->buildFactory()->buildDomainManager($ConnectionManagerMock);
        
        return [
            [$DomainManager, $ConnectionManagerMock]
        ];
    }

}
