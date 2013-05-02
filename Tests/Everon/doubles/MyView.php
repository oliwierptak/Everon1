<?php
namespace Everon\Test;

use Everon\Dependency;

class MyView extends \Everon\View
{
    use Dependency\Injection\Logger;
    
    public function testOne()
    {
        $this->setOutput('view action test one');
        $this->getLogger()->error('view error');
    }    
}