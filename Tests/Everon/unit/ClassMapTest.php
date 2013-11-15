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

use Everon\Interfaces;

class ClassMapTest extends \Everon\TestCase
{
    protected $filename = null;
    
    public function setUp()
    {
        @unlink($this->getTmpDirectory().'class_map_test.php');
    }

    public function testConstructor()
    {      
        $ClassMap = new \Everon\ClassMap($this->getTmpDirectory().'class_map_test.php');
        $this->assertInstanceOf('Everon\Interfaces\ClassMap', $ClassMap);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAddToMap(Interfaces\ClassMap $ClassMap)
    {
        $ClassMap->addToMap('test', 'test.php');
        $this->assertNotNull($ClassMap->getFilenameFromMap('test'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetFilenameFromMap(Interfaces\ClassMap $ClassMap)
    {
        $ClassMap->addToMap('test', 'test.php');
        $this->assertEquals('test.php', $ClassMap->getFilenameFromMap('test'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetFilenameFromMapShouldReturnNullWhenNotFound(Interfaces\ClassMap $ClassMap)
    {
        $this->assertNull($ClassMap->getFilenameFromMap('test'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testLoadMap(Interfaces\ClassMap $ClassMap)
    {
        $ClassMap->addToMap('test', 'test.php');
        
        $property = $this->getProtectedProperty('Everon\ClassMap', 'class_map');
        $property->setValue($ClassMap, null);
        
        $ClassMap->loadMap();
        
        $this->assertEquals('test.php', $ClassMap->getFilenameFromMap('test'));
    }

    public function dataProvider()
    {
        $filename = $this->getTmpDirectory().'class_map_test.php';
        
        $ClassMap = new \Everon\ClassMap($filename);
        return [
            [$ClassMap]
        ];
    }

}