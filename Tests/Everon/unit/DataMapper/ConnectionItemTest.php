<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\DataMapper;

use Everon\DataMapper\Connection;
use Everon\Interfaces;
use Everon\Helper;

class ConnectionItemTest extends \Everon\TestCase
{
    use Helper\Arrays;
    
    function testConstructor()
    {
        $connections = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'everon_test',
            'user' => 'everon',
            'password' => 'test',
            'encoding' => 'UTF8',
            'adapter_name' => 'MySql',
            'options' => [\PDO::ATTR_DRIVER_NAME => 'mysql_test']
        ];
        $ConnectionItem = new \Everon\DataMapper\Connection\Item($connections);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionItem', $ConnectionItem);
        $this->assertEquals('mysql', $ConnectionItem->getDriver());
        $this->assertEquals('MySql', $ConnectionItem->getAdapterName());
    }
    
    /**
     * @expectedException \Everon\DataMapper\Exception\ConnectionItem
     * @expectedExceptionMessage Missing required parameter: "driver"
     */   
    function testValidateShouldThrowAnExceptionWhenWrongData()
    {
        $connections = [];
        $ConnectionItem = new \Everon\DataMapper\Connection\Item($connections);
        $this->assertInstanceOf('Everon\DataMapper\Interfaces\ConnectionItem', $ConnectionItem);
    }

    /**
     * @dataProvider dataProvider
     */
    function testGetterAndSetters(\Everon\DataMapper\Interfaces\ConnectionItem $ConnectionItem, array $expected)
    {
        $this->assertEquals('mysql', $ConnectionItem->getDriver());
        $this->assertEquals('localhost', $ConnectionItem->getHost());
        $this->assertEquals('everon_test', $ConnectionItem->getDatabase());
        $this->assertEquals('everon', $ConnectionItem->getUsername());
        $this->assertEquals('test', $ConnectionItem->getPassword());
        $this->assertEquals('UTF8', $ConnectionItem->getEncoding());
        $this->assertEquals('MYSQL', $ConnectionItem->getAdapterName());
        $this->assertEquals('mysql:dbname=everon_test;host=localhost;port=3306', $ConnectionItem->getDsn());
        $this->assertEquals(
            $this->arrayMergeDefault(
                [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''], $expected['options']
            ),
            $ConnectionItem->getOptions()
        );
    }

    /**
     * @dataProvider dataProvider
     */
    function testToPdo(\Everon\DataMapper\Interfaces\ConnectionItem $ConnectionItem, array $expected)
    {
        $this->assertEquals([
            'mysql:dbname=everon_test;host=localhost;port=3306',
            'everon',
            'test',
            $this->arrayMergeDefault(
                [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''], $expected['options']
            )
        ], $ConnectionItem->toPdo());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\DataMapper\Exception\ConnectionItem
     * @expectedExceptionMessage Driver database not set
     */
    function testGetAdapterNameShouldThrowExceptionWhenAdapterNameNotSet(\Everon\DataMapper\Interfaces\ConnectionItem $ConnectionItem, array $expected)
    {
        $this->assertEquals('MYSQL', $ConnectionItem->getAdapterName());
        
        $AdapterName = $this->getProtectedProperty('Everon\DataMapper\Connection\Item', 'adapter_name');
        $AdapterName->setAccessible(true);
        $AdapterName->setValue($ConnectionItem, null);

        $DriverName = $this->getProtectedProperty('Everon\DataMapper\Connection\Item', 'driver');
        $DriverName->setAccessible(true);
        $DriverName->setValue($ConnectionItem, null);
        $this->assertEquals(null, $ConnectionItem->getAdapterName());
    }


    function dataProvider()
    {
        $connections = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'everon_test',
            'user' => 'everon',
            'password' => 'test',
            'encoding' => 'UTF8',
            'adapter_name' => 'MYSQL',
            'options' => [\PDO::ATTR_DRIVER_NAME => 'mysql_test']
        ];
        
        $ConnectionItem = $this->buildFactory()->buildConnectionItem($connections);
        
        return [
            [$ConnectionItem, $connections]
        ];
    }

}
