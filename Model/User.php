<?php
namespace Everon\Model;

use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;

class User
{
    //use Dependency\Injection\Request;
    
    public function getUser($username)
    {
        $data = [
            'id' => 1,
            'username' => $username,
            'password' => 'easy'
        ];

        return new \Everon\Helper\Popo($data);
    }

    public function authenticate($username, $password)
    {
        $User = $this->getUser($username);
        if ($User->getPassword() === $password) {
            return $User;
        }

        return null;
    }
    
/*    public function actionSubmit(Interfaces\View $View)
    {
        $username = $this->getRequest()->getPostParameter('username');
        $password = $this->getRequest()->getPostParameter('password');
        
        $View->set('User', function() use ($username, $password) {
            $User = $this->authenticate($username, $password);
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
    }    */
}
