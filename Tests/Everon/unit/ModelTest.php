<?php
namespace Everon\Test;

class ModelTest extends \Everon\TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testOne(\Everon\Test\MyModel $Model)
    {
        $Model->testOne();
    }

    public function dataProvider()
    {
        $Container = new \Everon\Dependency\Container();
        $Factory = new \Everon\Factory($Container);
        $Logger = $Factory->buildLogger($this->getLogDirectory());

        $Container->register('Logger', function() use ($Logger) {
            return $Logger;
        });

        $Model = $Factory->buildModel('MyModel', 'Everon\Test');
        return [
            [$Model]
        ];
    }

}
