<?php
namespace Everon\Test;

class EnvironmentTest extends \Everon\TestCase
{
   
    public function testConstructor()
    {
        $Environment = new \Everon\Environment(PROJECT_ROOT);
        $this->assertInstanceOf('\Everon\Interfaces\Environment', $Environment);
    }

    public function testGetters()
    {
        $Environment = new \Everon\Environment(PROJECT_ROOT);

        $this->assertEquals(PROJECT_ROOT, $Environment->getRoot());

        $this->assertEquals(PROJECT_ROOT.'Config'.DIRECTORY_SEPARATOR, $Environment->getConfig());
        $this->assertEquals(PROJECT_ROOT.'Model'.DIRECTORY_SEPARATOR, $Environment->getModel());
        $this->assertEquals(PROJECT_ROOT.'View'.DIRECTORY_SEPARATOR, $Environment->getView());
        $this->assertEquals(PROJECT_ROOT.'View'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR, $Environment->getViewTemplate());
        $this->assertEquals(PROJECT_ROOT.'Controller'.DIRECTORY_SEPARATOR, $Environment->getController());

        $this->assertEquals(PROJECT_ROOT.'Tests'.DIRECTORY_SEPARATOR, $Environment->getTest());

        $this->assertEquals(PROJECT_ROOT.'Src'.DIRECTORY_SEPARATOR, $Environment->getSource());
        $this->assertEquals(PROJECT_ROOT.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR, $Environment->getEveron());
        $this->assertEquals(PROJECT_ROOT.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR.'Interfaces'.DIRECTORY_SEPARATOR, $Environment->getEveronInterface());
        $this->assertEquals(PROJECT_ROOT.'Src'.DIRECTORY_SEPARATOR.'Everon'.DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR, $Environment->getEveronLib());

        $this->assertEquals(PROJECT_ROOT.'Tmp'.DIRECTORY_SEPARATOR, $Environment->getTmp());
        $this->assertEquals(PROJECT_ROOT.'Tmp'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR, $Environment->getLog());
        $this->assertEquals(PROJECT_ROOT.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR, $Environment->getCache());
        $this->assertEquals(PROJECT_ROOT.'Tmp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR, $Environment->getCacheConfig());
    }

}