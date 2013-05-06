<?php
namespace Everon\Controller;

use Everon\Controller;
use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{
    use Dependency\Injection\Logger;

    public function submit()
    {
        $username = $this->getRequest()->getPostParameter('username');
        $password = $this->getRequest()->getPostParameter('password');

        $this->getView()->set('User', function() use ($username, $password) {
            $User = $this->getModel('User')->authenticate($username, $password);
            if ($User === null) {
                $this->onInvalidLogin($username);
            }
            
            return $User;
        });
    }
    
    public function onInvalidLogin($username)
    {
        //do something with the model ...
        
        throw new Exception\DomainException(vsprintf(
            'Failed login attempt for user: "%s"', [$username]
        ));
    }



}