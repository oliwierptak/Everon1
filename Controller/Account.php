<?php
namespace Everon\Mvc\Controller;

use Everon\Mvc\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Account extends Controller implements Interfaces\Controller
{
    public function index()
    {
    }
    
    public function settings()
    {
        $this->getView()->set('View.Body', '<h3>settings</h3>');
    }
    
}