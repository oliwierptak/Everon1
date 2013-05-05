<?php
namespace Everon\Test;

class MyController extends \Everon\Controller
{
    public function testOne()
    {
        $this->setOutput('test one');
        return true;
    }

    public function beforeTestOne()
    {
        //$this->setOutput('before test one');
        return true;
    }

    public function afterTestOne()
    {
        $this->setOutput('after test one');
        return true;
    }

    public function testTwo()
    {
        $this->setOutput('test two');
        return true;
    }
}