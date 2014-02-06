<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Test\Config;

use Everon\Environment;

class ManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Matcher = $this->getMock('Everon\Interfaces\ConfigExpressionMatcher');
        $Loader = $this->getMock('Everon\Interfaces\ConfigLoader');
        $Manager = new \Everon\Config\Manager($Loader, $Matcher);
        $this->assertInstanceOf('Everon\Interfaces\ConfigManager', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRegister(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $count = count($ConfigManager->getConfigs());
        $ConfigManager->unRegister($Expected->getName());
        $this->assertCount($count - 1, $ConfigManager->getConfigs());
        
        $ConfigManager->register($Expected);
        $this->assertCount($count, $ConfigManager->getConfigs());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testUnRegister(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $count = count($ConfigManager->getConfigs());
        $ConfigManager->unRegister($Expected->getName());

        $this->assertCount($count - 1, $ConfigManager->getConfigs());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Config
     * @expectedExceptionMessage Config with name: "test" already registered
     */
    public function testRegisterShouldThrowExceptionWhenConfigAlreadyExists(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->register($Expected);
        $ConfigManager->register($Expected);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoadAndRegisterConfigs(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('application');
        $this->assertInstanceOf('Everon\Config', $Config);

        $Config = $ConfigManager->getConfigByName('router');
        $this->assertInstanceOf('Everon\Config\Router', $Config);

        $Config = $ConfigManager->getConfigByName('test');
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
        $this->assertEquals($Expected->toArray(), $Config->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('application');
        $this->assertInstanceOf('Everon\Config', $Config);
        
        $Config = $ConfigManager->getConfigByName('router');
        $this->assertInstanceOf('Everon\Config\Router', $Config);
        
        $Config = $ConfigManager->getConfigByName('test');
        $this->assertInstanceOf('Everon\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Config
     * @expectedExceptionMessage Invalid config name: wrong
     */
    public function testGetConfigByNameShouldThrowExceptionWhenConfigFileNotFound(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getConfigByName('wrong');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRegisterWithCache(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->getConfigLoader()->disableCache();
        $ConfigManager->getConfigLoader()->enableCache();
        $ConfigManager->unRegister($Expected->getName());
        $ConfigManager->register($Expected);
        $Config = $ConfigManager->getConfigByName($Expected->getName());
        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoadAndRegisterConfigsWithCache(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->getConfigLoader()->enableCache();

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);
        $Config = $ConfigManager->getConfigByName('application');

        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetConfigsWithCache(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->getConfigLoader()->enableCache();

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);

        $configs = $ConfigManager->getConfigs();
        $this->assertNotEmpty($configs);
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();

        //$name, Interfaces\ConfigLoaderItem $ConfigLoaderItem, \Closure $Compiler
        $Compiler = function(&$data) {};

        $filename = $this->getConfigDirectory().'test.ini';
        $ConfigLoaderItem = new \Everon\Config\Loader\Item($filename, parse_ini_file($filename, true));
        $Expected = new \Everon\Config(
            'test',
            $ConfigLoaderItem,
            $Compiler
        );
        
        $Loader = new \Everon\Config\Loader($this->getConfigDirectory(), $this->getConfigCacheDirectory());
        $Loader->setFactory($Factory);
        
        $ConfigManager = new \Everon\Config\Manager($Loader);
        $ConfigManager->setFactory($Factory);
        
        //todo add setter in TestCase for setting up Environment for tests
        $Environment = new Environment($this->Environment->getRoot());
        $Environment->setConfig($this->getConfigDirectory());
        $Environment->setCacheConfig($this->getConfigCacheDirectory());
        $ConfigManager->setEnvironment($Environment);
        
        return [
            [$ConfigManager, $Expected]
        ];
    }

}
