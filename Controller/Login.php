<?php

namespace Everon\Mvc\Controller;

use Everon\Mvc\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{
    public function form()
    {
        $redirect_url = urlencode('/login/reset password');
        $this->getViewManager()->getView('Login')->set('form_action_url', "login/submit/session/adf24ds34/redirect/${redirect_url}?token=something&pif=2457");
        $this->getView()->set('canShowInfo', true);
    }
    
    public function account()
    {
        
    }

    public function submit()
    {
        $username = $this->getRequest()->getPostParameter('username');
        $password = $this->getRequest()->getPostParameter('password');

        $User = $this->getModelManager()->getModel('User')->authenticate($username, $password);
        if ($User === null) {
            $this->getLogger()->auth('Login failed for: "%s"', [$username]);
            return false;
        }

        $this->getView()->set('redirect_url', '/account/summary');
        $this->getView()->set('User', $User);
        //$this->getView()->set('Account', $this->getViewManager()->getView('Account'));
       

        // return $this->getViewManager()->getView('Account')->execute(['loginForm' => $this]);
    }

}