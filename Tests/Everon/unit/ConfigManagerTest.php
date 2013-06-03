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

class ConfigManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $Matcher = $this->getMock('Everon\Interfaces\ConfigExpressionMatcher');
        $Loader = $this->getMock('Everon\Interfaces\ConfigLoader');
        $Manager = new \Everon\Config\Manager($Loader, $Matcher);
        $this->assertInstanceOf('\Everon\Interfaces\ConfigManager', $Manager);
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
        $Config = $ConfigManager->getApplicationConfig();
        $this->assertInstanceOf('\Everon\Config', $Config);

        $Config = $ConfigManager->getRouterConfig();
        $this->assertInstanceOf('\Everon\Config\Router', $Config);

        $Config = $ConfigManager->getViewConfig();
        $this->assertInstanceOf('\Everon\Config\View', $Config);

        $Config = $ConfigManager->getConfigByName('test');
        $this->assertInstanceOf('\Everon\Interfaces\Config', $Config);
        $this->assertEquals($Expected->toArray(), $Config->toArray());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSettersAndGetters(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $Config = $ConfigManager->getApplicationConfig();
        $this->assertInstanceOf('Everon\Config', $Config);
        
        $Config = $ConfigManager->getRouterConfig();
        $this->assertInstanceOf('Everon\Config\Router', $Config);

        $Config = $ConfigManager->getViewConfig();
        $this->assertInstanceOf('Everon\Config\View', $Config);
        
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
        $ConfigManager->disableCache();
        $ConfigManager->enableCache();
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
        $ConfigManager->enableCache();

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);
        $Config = $ConfigManager->getApplicationConfig();

        $this->assertInstanceOf('Everon\Interfaces\Config', $Config);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetConfigsWithCache(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $ConfigManager->enableCache();

        $Property = $this->getProtectedProperty('Everon\Config\Manager', 'configs');
        $Property->setValue($ConfigManager, null);

        $configs = $ConfigManager->getConfigs();
        $this->assertNotEmpty($configs);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetupCachingAndDefaultConfig(\Everon\Interfaces\ConfigManager $ConfigManager, \Everon\Interfaces\Config $Expected)
    {
        $PropertyDefaultConfigName = $this->getProtectedProperty('Everon\Config\Manager', 'default_config_name');
        $PropertyDefaultConfigName->setValue($ConfigManager, $Expected->getName());

        $PropertyConfigs = $this->getProtectedProperty('Everon\Config\Manager', 'configs');

        $configs = [$Expected->getName() => $Expected];
        $PropertyConfigs->setValue($ConfigManager, $configs);

        $Method = $this->getProtectedMethod('Everon\Config\Manager', 'setupCachingAndDefaultConfig');
        $Method->invoke($ConfigManager);

        $this->assertInstanceOf('Everon\Config', $ConfigManager->getApplicationConfig());
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\DependencyContainer $Container
         * @var \Everon\Interfaces\Factory $Factory
         */
        list($Container, $Factory) = $this->getContainerAndFactory();

        $Expected = new \Everon\Config(
            'test',
            $this->getConfigDirectory().'test.ini',
            ['test'=>1]
        );
        
        $ConfigManager = $Container->resolve('ConfigManager');

        return [
            [$ConfigManager, $Expected]
        ];
    }

}
