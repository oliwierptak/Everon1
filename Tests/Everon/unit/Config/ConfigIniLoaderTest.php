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

use Everon\Interfaces;

class IniLoaderTest extends \Everon\TestCase
{
    protected function setUp()
    {
        if (is_dir($this->getTmpDirectory()) === false) {
            @mkdir($this->getTmpDirectory(), 0775, true);
        }
    }
    
    public function testConstructor()
    {
        $Loader = new \Everon\Config\Loader($this->Environment->getConfig(), $this->Environment->getCacheConfig());
        $this->assertInstanceOf('Everon\Interfaces\ConfigLoader', $Loader);
        $this->assertEquals($Loader->getConfigDirectory(), $this->Environment->getConfig());
        $this->assertEquals($Loader->getCacheDirectory(), $this->Environment->getCacheConfig());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoad(Interfaces\ConfigLoader $ConfigLoader)
    {
        $config_list = $ConfigLoader->load(false);
        $this->assertInternalType('array', $config_list);

        /**
         * @var Interfaces\ConfigLoaderItem $ConfigItem
         */
        $ConfigItem = $config_list['test']; 
        $config_filename = $ConfigItem->getFilename();
        $config_data = $ConfigItem->getData();
        
        $this->assertEquals($ConfigLoader->getConfigDirectory().'test.ini', $config_filename);
        $this->assertInternalType('array', $config_data);
        $this->assertEquals(1, $config_data['section']['test']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testLoadWithCache(Interfaces\ConfigLoader $ConfigLoader)
    {
        file_put_contents($ConfigLoader->getCacheDirectory().'test.ini.php', "<?php \$cache = array ('test' => 2);");

        $config_list = $ConfigLoader->load(true);

        /**
         * @var Interfaces\ConfigLoaderItem $ConfigItem
         */
        $ConfigItem = $config_list['test'];
        $config_filename = $ConfigItem->getFilename();
        $config_data = $ConfigItem->getData();
        
        $this->assertInternalType('array', $config_list);
        $this->assertInternalType('array', $config_data);

        $this->assertEquals($ConfigLoader->getConfigDirectory().'test.ini', $config_filename);        
        $this->assertInternalType('array', $config_data);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRead(Interfaces\ConfigLoader $ConfigLoader)
    {
        $filename = $this->getConfigDirectory().'application.ini';
        $data = $ConfigLoader->read($filename);
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testSaveConfigToCache(Interfaces\ConfigLoader $ConfigLoader)
    {
        $filename = $this->getConfigCacheDirectory().'application.ini';
        $cache_filename = $this->getConfigCacheDirectory().'application.ini.php';
        $ConfigMock = $this->getMock('Everon\Interfaces\Config', [], [], '', false);
        
        $ConfigMock->expects($this->once())
            ->method('getFilename')
            ->will($this->returnValue($filename));
        
        $ConfigMock->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue(['test'=>1]));
        
        $ConfigLoader->saveConfigToCache($ConfigMock);
        
        include($cache_filename);
        $this->assertInternalType('array', $cache);
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Everon\Exception\Config
     * @expectedExceptionMessage Unable to save config cache file: "test.ini"
     */
    public function testSaveConfigToCacheShouldThrowExceptionOnError(Interfaces\ConfigLoader $ConfigLoader)
    {
        $ex = new \Exception();
        $ConfigMock = $this->getMock('Everon\Interfaces\Config', [], [], '', false);

        $ConfigMock->expects($this->once())
            ->method('toArray')
            ->will($this->throwException($ex));

        $ConfigMock->expects($this->exactly(2))
            ->method('getFilename')
            ->will($this->returnValue('test.ini'));

        $ConfigLoader->saveConfigToCache($ConfigMock);
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->buildFactory();
        $ConfigLoader = $Factory->buildConfigLoader($this->getConfigDirectory(), $this->getConfigCacheDirectory());

        return [
            [$ConfigLoader]
        ];
    }

}
