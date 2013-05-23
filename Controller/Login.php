<?php
namespace Everon\Controller;

use Everon\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{
    use Dependency\Injection\Logger;
    
    public function form()
    {
        $this->getView()->set('redirect_url', urlencode('login'));
    }

    public function submit()
    {
        $username = $this->getRequest()->getPostParameter('username');
        $password = $this->getRequest()->getPostParameter('password');

        $User = $this->getModel('User')->authenticate($username, $password);
        if ($User === null) {
            $this->getLogger()->auth('Login failed for: "%s"', [$username]);
            return false;
        }

        $this->getView()->set('redirect_url', 'account/summary');
        $this->getView()->set('User', $User);
    }

}