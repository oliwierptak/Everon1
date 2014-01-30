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

use Everon\Test\Domain\Example\Repository;
use Everon\Domain\Interfaces;

class RepositoryTest extends \Everon\TestCase
{
    /**
     * @dataProvider222 dataProvider22
     */    
    public function testConstructor()
    {
        $DataMapperMock = $this->getMock('Everon\Interfaces\DataMapper');
        $Repository = new Repository('Example', $DataMapperMock);
        $this->assertInstanceOf('\Everon\Domain\Interfaces\Repository', $Repository);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldAddNewEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('Example', null, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPersistShouldUpdateEntityAndMarkEntityAsPersisted(Repository $Repository, array $data)
    {
        $Entity = $this->getFactory()->buildDomainEntity('Example', 1, $data, 'Everon\Test\Domain');
        $Repository->persist($Entity);
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetNameShouldReturnClassName(Repository $Repository, array $data)
    {
        $this->assertEquals('Example', $Repository->getName());
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
               
        $Repository = $this->getFactory()->buildDomainRepository('Example', $DataMapperMock, 'Everon\Test\Domain');
                    
        return [
            [$Repository, $entity_data]
        ];
    }
}