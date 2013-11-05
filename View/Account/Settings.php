<?php
namespace Everon\View\Account;

class Settings extends \Everon\View
{
    public function index()
    {
        $this->setContainer('Settings for {User.username}');
    }
}
