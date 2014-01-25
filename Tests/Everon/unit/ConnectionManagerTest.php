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

use Everon\Environment;

class ConnectionManagerTest extends \Everon\TestCase
{
    public function testConstructor()
    {
        $DatabaseConfig = $this->getFactory()->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('database');
        $connections = $DatabaseConfig->toArray();
        $Manager = new \Everon\DataMapper\Connection\Manager($connections);
        //$this->assertInstanceOf('\Everon\Interfaces\ConfigManager', $Manager);
    }

    public function dataProvider()
    {
        /**
         * @var \Everon\Interfaces\Factory $Factory
         */
        $Factory = $this->getFactory();

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
