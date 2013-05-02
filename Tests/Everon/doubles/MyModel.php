<?php
namespace Everon\Test;

use Everon\Dependency;

class MyModel
{
    use Dependency\Injection\Logger;

    public function testOne()
    {
        $this->getLogger()->debug('some debug');
    }
}