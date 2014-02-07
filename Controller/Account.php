<?php
namespace Everon\Mvc\Controller;

use Everon\Mvc\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Account extends Controller implements Interfaces\Controller
{
    public function index()
    {
        $this->getView()->set('title', 'Account');
    }
    
    public function settings()
    {
        $this->getView()->set('title', 'Settings');
    }
    
}