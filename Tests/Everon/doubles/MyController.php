<?php
namespace Everon\Test;

class MyController extends \Everon\Controller
{
    protected $models = ['MyModel'];
    
    public function initModel()
    {
        $this->models = [
            'test' => $this->getModel('MyModel')
        ];
    }

    public function testOne()
    {
        $this->setOutput('test one');
        return true;
    }

    public function beforeTestOne()
    {
        $this->getResponse()->setData('before test one');
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