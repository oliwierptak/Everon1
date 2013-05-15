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

class ConfigIniLoaderTest extends \Everon\TestCase
{
    protected function setUp()
    {
        if (is_dir($this->getTempDirectory()) === false) {
            @mkdir($this->getTempDirectory(), 0775, true);
        }
    }    
    
    public function testConstructor()
    {
        $Manager = new \Everon\Config\Loader($this->Environment->getConfig(), $this->Environment->getCacheConfig());
        $this->assertInstanceOf('\Everon\Interfaces\ConfigLoader', $Manager);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetList(Interfaces\ConfigLoader $ConfigLoader)
    {
        $Compiler = function(){};
        $list = $ConfigLoader->getData($Compiler, false, 'application.ini');
        $this->assertInternalType('array', $list);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRead(Interfaces\ConfigLoader $ConfigLoader)
    {
        $filename = $this->getConfigDirectory().'application.ini';
        $data = $ConfigLoader->read($filename);
        $this->assertInternalType('array', $data);
    }
    
    /**
     * @dataProvider dataProvider
     */
    public function testSaveConfigToCache(Interfaces\ConfigLoader $ConfigLoader)
    {
        $filename = $this->getConfigCacheDirectory().'application.ini';
        $cache_filename = $this->getConfigCacheDirectory().'application.ini.php';
        $ConfigMock = $this->getMock('Everon\Interfaces\Config');
        
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

    
    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\DependencyContainer $Container
         * @var \Everon\Interfaces\Factory $Factory
         */
        list($Container, $Factory) = $this->getContainerAndFactory();
        
        $ConfigLoader = $Factory->buildConfigLoader($this->getConfigDirectory(), $this->getConfigCacheDirectory());

        return [
            [$ConfigLoader]
        ];
    }

}
