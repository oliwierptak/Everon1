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

use Everon\Test\Domain\User\Repository;
use Everon\Domain\Interfaces;

class RepositoryTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $Repository = new Repository('User', $DataMapperMock);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldAddNewEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->buildFactory()->buildDomainEntity('User', null, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldUpdateEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->buildFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testRemoveShouldDeleteEntityAndMarkEntityAsDeleted(Repository $Repository, array $data)
    {
        $Entity = $this->buildFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
        $Repository->remove($Entity);
        $this->assertNull($Entity->getId());
        $this->assertTrue($Entity->isDeleted());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testSetMapperShouldSetMapper(Repository $Repository, array $data)
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $Repository->setMapper($DataMapperMock);
        $this->assertInstanceOf('Everon\Interfaces\DataMapper', $Repository->getMapper());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFetchEntityByIdShouldReturnEntity(Repository $Repository, array $data)
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $DataMapperMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([$data]));
        $DataMapperMock->expects($this->once())
            ->method('getAndValidateId')
            ->will($this->returnValue(1));
        $DataMapperMock->expects($this->once())
            ->method('getAndValidateId')
            ->will($this->returnValue(1));

        $EntityMock = $this->getMock('Everon\Domain\Interfaces\Entity');
        $DomainManagerMock = $Repository->getDomainManager();
        $DomainManagerMock->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($EntityMock));
        
        $Repository->setMapper($DataMapperMock);
        $EntityMock = $Repository->fetchEntityById(1);
        
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $EntityMock);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnClassName(Repository $Repository, array $data)
    {
        $this->assertEquals('User', $Repository->getName());
    }
   
    public function dataProvider()
    {
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter');
        $PdoAdapterMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue([
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]));
        
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $SchemaMock->expects($this->once())
            ->method('getDatabase')
            ->will($this->returnValue('phpunit_db_test'));
        $SchemaMock->expects($this->once())
            ->method('getPdoAdapterByName')
            ->will($this->returnValue($PdoAdapterMock));
        
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $TableMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test_table'));

        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $DataMapperMock->expects($this->once())
            ->method('getSchemaTable')
            ->will($this->returnValue($TableMock));

        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
               
        $Factory = $this->buildFactory();
        $Container = $Factory->getDependencyContainer();
        $DomainManagerMock = $this->getMock('Everon\Test\Domain\Interfaces\Manager', [], [], '', false);
        $Container->register('DomainManager', function() use ($DomainManagerMock) {
            return $DomainManagerMock;
        });
        
        $Repository = $Factory->buildDomainRepository('User', $DataMapperMock, 'Everon\Test\Domain');
        
        return [
            [$Repository, $entity_data]
        ];
    }
}