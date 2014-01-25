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

use Everon\Test\MyRepository;
use Everon\Domain\Interfaces;

class RepositoryTest extends \Everon\TestCase
{
    /**
     * @dataProvider222 dataProvider22
     */    
    public function testConstructor()
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $Repository = new MyRepository\Repository('MyRepository', $DataMapperMock);
        $this->assertInstanceOf('\Everon\Domain\Interfaces\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldAddNewEntityAndMarkEntityAsPersisted(MyRepository\Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('MyEntity', null, $data, 'Everon\Test');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldUpdateEntityAndMarkEntityAsPersisted(MyRepository\Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('MyEntity', 1, $data, 'Everon\Test');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnClassName(MyRepository\Repository $Repository, array $data)
    {
        $this->assertEquals('MyRepository', $Repository->getName());
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
            ->method('getPdoAdapter')
            ->will($this->returnValue($PdoAdapterMock));
        
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table');
        $TableMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test_table'));

        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $DataMapperMock->expects($this->once())
            ->method('getTable')
            ->will($this->returnValue($TableMock));
        
        $DataMapperMock->expects($this->once())
            ->method('getSchema')
            ->will($this->returnValue($SchemaMock));

        $entity_data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
               
        $Repository = $this->getFactory()->buildDomainRepository('MyRepository', $DataMapperMock, 'Everon\Test');
                    
        return [
            [$Repository, $entity_data]
        ];
    }
}