<?php
namespace Everon\View;

use Everon\View;
use Everon\View\Index as DefaultView;

class Account extends DefaultView
{
    public function index()
    {
        $this->set('View.title', 'Account');
        $this->set('View.body', 'Welcome to your account');
    }

    public function settings()
    {
        $this->set('title', 'Settings');
    }
}
