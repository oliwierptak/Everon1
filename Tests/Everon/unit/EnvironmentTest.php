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

class EnvironmentTest extends \Everon\TestCase
{
   
    public function testConstructor()
    {
        $Environment = new \Everon\Environment('testing');
        $this->assertInstanceOf('Everon\Interfaces\Environment', $Environment);
    }

    public function testGetters()
    {
        $root = $this->Environment->getRoot();
        $Environment = new \Everon\Environment($root);

        $this->assertEquals($root, $Environment->getRoot());

        $this->assertEquals($root.'Config'.DIRECTORY_SEPARATOR, $Environment->getConfig());
        $this->assertEquals($root.'DataMapper'.DIRECTORY_SEPARATOR, $Environment->getDataMapper());
        $this->assertEquals($root.'Domain'.DIRECTORY_SEPARATOR, $Environment->getDomain());
        $this->assertEquals($root.'View'.DIRECTORY_SEPARATOR, $Environment->getView());
        $this->assertEquals($root.'Controller'.DIRECTORY_SEPARATOR, $Environment->getController());

        $this->assertEquals($root.'Tests'.DIRECTORY_SEPARATOR, $Environment->getTest());

        $this->assertEquals($root.'Src'.DIRECTORY_SEPARATOR, $Environment->getSource());
        $this->assertEquals($root.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR, $Environment->getEveron());
        $this->assertEquals($root.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR.'Interfaces'.DIRECTORY_SEPARATOR, $Environment->getEveronInterface());
        $this->assertEquals($root.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR, $Environment->getEveronConfig());

        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR, $Environment->getTmp());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR, $Environment->getLog());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR, $Environment->getCache());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR, $Environment->getCacheConfig());
        $this->assertEquals($root.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR, $Environment->getCacheView());
    }
    
    public function testSetters()
    {
        $Environment = new \Everon\Environment('testing');
        
        $Environment->setRoot('test');
        $this->assertEquals('test', $Environment->getRoot());
        
        $Environment->setConfig('test');
        $this->assertEquals('test', $Environment->getConfig());
        
        $Environment->setDomain('test');
        $this->assertEquals('test', $Environment->getDomain());
        
        $Environment->setView('test');
        $this->assertEquals('test', $Environment->getView());
        
        $Environment->setController('test');
        $this->assertEquals('test', $Environment->getController());

        $Environment->setTest('test');
        $this->assertEquals('test', $Environment->getTest());

        $Environment->setSource('test');
        $this->assertEquals('test', $Environment->getSource());
        
        $Environment->setEveron('test');
        $this->assertEquals('test', $Environment->getEveron());
        
        $Environment->setEveronInterface('test');
        $this->assertEquals('test', $Environment->getEveronInterface());
        
        $Environment->setEveronLib('test');
        $this->assertEquals('test', $Environment->getEveronConfig());

        $Environment->setTmp('test');
        $this->assertEquals('test', $Environment->getTmp());
        
        $Environment->setLog('test');
        $this->assertEquals('test', $Environment->getLog());
        
        $Environment->setCache('test');
        $this->assertEquals('test', $Environment->getCache());
        
        $Environment->setCacheConfig('test');
        $this->assertEquals('test', $Environment->getCacheConfig());
    }

}