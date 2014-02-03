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
        $this->assertInstanceOf('\Everon\Domain\Interfaces\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldAddNewEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('User', null, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldUpdateEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testRemoveShouldDeleteEntityAndMarkEntityAsDeleted(Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
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
            ->method('getName')
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
        
        $DataMapperMock->expects($this->once())
            ->method('getSchema')
            ->will($this->returnValue($SchemaMock));

        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
               
        $Repository = $this->getFactory()->buildDomainRepository('User', $DataMapperMock, 'Everon\Test\Domain');
                    
        return [
            [$Repository, $entity_data]
        ];
    }
}