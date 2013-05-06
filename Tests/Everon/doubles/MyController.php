<?php
namespace Everon\Test;

class MyController extends \Everon\Controller
{
    public function beforeTestOne()
    {
        $this->setOutput('before test one');
    }

    public function testOne()
    {
        $this->setOutput('test one');
    }

    public function afterTestOne()
    {
        $this->setOutput('after test one');
    }

}