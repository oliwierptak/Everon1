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

class DataMapperTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema');
        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [],[],'', false);
        
        $DataMapper = new DataMapper\User($TableMock, $SchemaMock);
        $this->assertInstanceOf('\Everon\Interfaces\DataMapper', $DataMapper);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAddShouldInsertEntity($Mapper, $PdoAdapterMock)
    {
/*        $PdoAdapterMock->expects($this->once())
            ->method('exec')
            ->will($this->returnValue([
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]));*/
    }
  
    public function dataProvider()
    {
        $PdoAdapterMock = $this->getMock('Everon\Interfaces\PdoAdapter', [], [], '', false);

        $SchemaMock = $this->getMock('Everon\DataMapper\Interfaces\Schema', [], [], '', false);
        $SchemaMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('phpunit_db_test'));

        $SchemaMock->expects($this->once())
            ->method('getPdoAdapterByName')
            ->will($this->returnValue($PdoAdapterMock));

        $TableMock = $this->getMock('Everon\DataMapper\Interfaces\Schema\Table', [],[], '', false);
        $TableMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('my_test_table'));
        
        $Table = $this->getFactory()->buildSchemaTable('user', [], [], []);
        $Mapper = $this->getFactory()->buildDataMapper($Table, $SchemaMock, 'Everon\Test\DataMapper');
        
        return [
            [$Mapper, $PdoAdapterMock]
        ];
    }
}