<?php
namespace Everon\Test;

class MyView extends \Everon\View
{
    public function testOne()
    {
        $this->setOutput('view action test one');
    }
}