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

class ConfigTest extends \Everon\TestCase
{

    public function testConstructor()
    {
        $filename = $this->getConfigDirectory().'test.ini';
        $ConfigLoaderItem = new \Everon\Config\Loader\Item($filename, parse_ini_file($filename, true));

        $Compiler = function(&$item) {};
        $Config = new \Everon\Config('test', $ConfigLoaderItem, $Compiler);
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Interfaces\Config $Config)
    {
        $Config->setFilename('test.ini');
        $Config->setName('test');
        
        $this->assertEquals('test.ini', $Config->getFilename());
        $this->assertEquals('test', $Config->getName());
        
        $this->assertEquals('1', $Config->go('section')->get('test'));
        $this->assertInternalType('array', $Config->go('another_section')->get('some'));
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testGetNonExistingValueShouldReturnNullOrDefault(\Everon\Interfaces\Config $Config)
    {
        //todo $Config->get() return default value even when section was not found
        $this->assertEquals('1', $Config->go('section')->get('test'));
        $this->assertEquals(null, $Config->go('section')->get('foo'));
        $this->assertEquals(null, $Config->go('section')->get('more error'));
        $this->assertEquals('default', $Config->go('section')->get('foobar', 'default'));
    }

    public function dataProvider()
    {
        $filename = $this->getConfigDirectory().'test.ini';
        $ConfigLoaderItem = new \Everon\Config\Loader\Item($filename, parse_ini_file($filename, true));
        $Compiler = function(&$item) {};        
        $Config = new \Everon\Config('test', $ConfigLoaderItem, $Compiler);
        $Config->setFactory($this->buildFactory());
        
        return [
            [$Config]
        ];
    }

}
