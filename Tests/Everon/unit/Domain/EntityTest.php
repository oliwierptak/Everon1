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

use Everon\Test\Domain\User\Entity;
use Everon\Domain\Interfaces;

class EntityTest extends \Everon\TestCase
{
    /**
     * @dataProvider dataProvider
     */    
    function testConstructor(Entity $Entity, array $data)
    {
        $Entity = new \Everon\Domain\Entity(1, $data);
        $this->assertInstanceOf('Everon\Domain\Interfaces\Entity', $Entity);
        $this->assertEquals(1, $Entity->getId());
    }

    /**
     * @dataProvider dataProvider
     */
    function testEntityStateShouldBeNewWhenIdNotSet(Entity $Entity, array $data)
    {
        $Entity = new \Everon\Domain\Entity(null, $data);
        $this->assertNull($Entity->getId());
        $this->assertTrue($Entity->isNew());
        $this->assertFalse($Entity->isDeleted());
        $this->assertFalse($Entity->isModified());
        $this->assertFalse($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    function testStateShouldBePersistedWhenIdIsSet(Entity $Entity, array $data)
    {
        $this->assertNotNull($Entity->getId());
        $this->assertTrue($Entity->isPersisted());
        $this->assertFalse($Entity->isNew());
        $this->assertFalse($Entity->isDeleted());
        $this->assertFalse($Entity->isModified());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testShouldReturnValueByGetter(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getFirstName());
        $this->assertEquals($data['date_of_birth'], $Entity->getDateOfBirth());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testShouldMarkModifiedProperties(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getFirstName());
        $this->assertEquals($data['last_name'], $Entity->getLastName());
        
        $Entity->setFirstName('Tom');
        $Entity->setLastName('Smith');

        $this->assertEquals('Tom', $Entity->getFirstName());
        $this->assertEquals('Smith', $Entity->getLastName());
        
        $this->assertNotEmpty($Entity->getModifiedProperties());
        $this->assertCount(2, $Entity->getModifiedProperties());
        
        $this->assertTrue($Entity->isPropertyModified('first_name'));
        $this->assertFalse($Entity->isPropertyModified('date_of_birth'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testGetValueByNameShouldReturnValue(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getValueByName('first_name'));
        $this->assertEquals($data['last_name'], $Entity->getValueByName('last_name'));
    }

    /**
     * @dataProvider dataProvider
     */
    function testSetValueByNameShouldSetValue(Entity $Entity, array $data)
    {
        $Entity->setValueByName('first_name', 'Tom');
        $Entity->setValueByName('last_name', 'Smith');
        
        $this->assertEquals('Tom', $Entity->getFirstName());
        $this->assertEquals('Smith', $Entity->getLastName());
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testDeleteShouldResetStateAndMarkAsDeleted(Entity $Entity, array $data)
    {
        $Entity->delete();
        
        $this->assertNull($Entity->getId());
        $this->assertNull($Entity->getModifiedProperties());
        $this->assertNull($Entity->getData());
        $this->assertTrue($Entity->isDeleted());
    }

    /**
     * @dataProvider dataProvider
     */
    function testPersistShouldSetIdAndDataAndMarkAsPersisted(Entity $Entity, array $data)
    {
        $Entity->persist(12, $data);

        $this->assertEquals(12, $Entity->getId());
        $this->assertNull($Entity->getModifiedProperties());
        $this->assertEquals($data, $Entity->getData());
        $this->assertTrue($Entity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Domain\Exception\Entity
     * @expectedExceptionMessage It is the database's job to maintain primary keys
     */
    function testSetIdShouldThrowException(Entity $Entity, array $data)
    {
        $Entity->setId(1);
    }
    
    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Domain\Exception\Entity
     * @expectedExceptionMessage Invalid property name: i dont exist
     */
    function testGetValueByNameShouldThrowExceptionWhenKeyDoesNotExist(Entity $Entity, array $data)
    {
        $Entity->getValueByName('i dont exist');
    }
    
    /**
     * @dataProvider dataProvider
     */
    function testSerializeUnserialize(Entity $Entity, array $data)
    {
        $serialized = serialize($Entity);
        $UnserializedEntity = unserialize($serialized);
        
        $this->assertEquals(1, $UnserializedEntity->getId());
        $this->assertTrue($UnserializedEntity->isPersisted());
    }

    /**
     * @dataProvider dataProvider
     */
    function testVarExport(Entity $Entity, array $data)
    {
        $exported = var_export($Entity, 1);
        $eval = eval('$ExportedEntity = '.$exported.';');
        $this->assertEquals($Entity, $ExportedEntity);
    }
    
    function dataProvider()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
        
        $Entity = $this->buildFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
                    
        return [
            [$Entity, $data]
        ];
    }
}