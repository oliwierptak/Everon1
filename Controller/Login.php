<?php

namespace Everon\Mvc\Controller;

use Everon\Mvc\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{
    public function form()
    {
        $redirect_url = '/url encoded';
        $this->getViewManager()->getView('Login')->set('form_action_url', "login/submit/session/adf24ds34/redirect/${redirect_url}?token=something&pif=2457");
        $this->getView()->set('canShowInfo', true);
    }
    
    public function account()
    {
        
    }

    public function submit()
    {
        $email = $this->getRequest()->getPostParameter('email');
        $password = $this->getRequest()->getPostParameter('password');

        $this->getView()->set('Login', new \Everon\Helper\Popo([])); //tmp, for testing templates

        $User = $this->getDomainManager()->getModel('User')->authenticate($email, $password);
        if ($User === null) {
            return false;
        }

        $this->getView()->set('User', $User);
        $this->getView()->set('View.redirectUrl', '/account');
        //$this->getView()->set('Account', $this->getViewManager()->getView('Account'));
       

        // return $this->getViewManager()->getView('Account')->execute(['loginForm' => $this]);
    }

}