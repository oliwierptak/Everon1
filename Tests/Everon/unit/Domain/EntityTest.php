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

use Everon\Test\Domain\Example\Entity;
use Everon\Domain\Interfaces;

class EntityTest extends \Everon\TestCase
{
    /**
     * @dataProvider dataProvider
     */    
    public function testConstructor(Entity $Entity, array $data)
    {
        $Entity = new \Everon\Domain\Entity(1, $data);
        $this->assertInstanceOf('\Everon\Domain\Interfaces\Entity', $Entity);
        $this->assertEquals(1, $Entity->getId());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEntityStateShouldBeNewWhenIdNotSet(Entity $Entity, array $data)
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
    public function testEntityStateShouldBePersistedWhenIdIsSet(Entity $Entity, array $data)
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
    public function testEntityShouldReturnValueByGetter(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getFirstName());
        $this->assertEquals($data['date_of_birth'], $Entity->getDateOfBirth());
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testEntityShouldMarkModifiedProperties(Entity $Entity, array $data)
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
    public function testEntityGetValueByNameShouldReturnValue(Entity $Entity, array $data)
    {
        $this->assertEquals($data['first_name'], $Entity->getValueByName('first_name'));
        $this->assertEquals($data['last_name'], $Entity->getValueByName('last_name'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEntitySetValueByNameShouldSetValue(Entity $Entity, array $data)
    {
        $Entity->setValueByName('first_name', 'Tom');
        $Entity->setValueByName('last_name', 'Smith');
        
        $this->assertEquals('Tom', $Entity->getFirstName());
        $this->assertEquals('Smith', $Entity->getLastName());
    }

    public function dataProvider()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-09-09',
        ];
        
        $Entity = $this->getFactory()->buildDomainEntity('User', 1, $data, 'Everon\Test\Domain');
                    
        return [
            [$Entity, $data]
        ];
    }
}