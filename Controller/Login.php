<?php
namespace Everon\Controller;

use Everon\Controller;
use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{
    use Dependency\Injection\Logger;

    public function form()
    {
/*        $FormElement = new \Everon\View\Element\Form([
            'action' => 'login/submit/session/adf24ds34/redirect/account%5Csummary?and=something&else=2457'
        ]);
        $Output = $this->getView()->getTemplate('Form', [
            'Form' => $FormElement,
        ]);
        $this->setOutput($Output);*/
        
        return true;
    }

    public function submit()
    {
        $username = $this->getRequest()->getPostParameter('username');
        $password = $this->getRequest()->getPostParameter('password');

        $this->getView()->set('User', function() use ($username, $password) {
            $User = $this->getModel('User')->authenticate($username, $password);
            if ($User === null) {
                $this->onSubmitError($username);
            }
            
            return $User;
        });

        return true;
    }
    
    public function onSubmitError($username)
    {
        throw new Exception\DomainException(vsprintf(
            'Failed login attempt for user: "%s"', [$username]
        ));
    }



}