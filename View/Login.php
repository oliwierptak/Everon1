<?php
namespace Everon\View;

use Everon\View;

class Login extends View
{
    public function form()
    {
        $redirect_url = urlencode('/login/reset password');
        $this->set('login_url', $this->url("login/submit/session/adf24ds34/redirect/${redirect_url}?and=something&else=2457"));
        $FormElement = new \Everon\View\Html\Form([
            'action' => $this->get('login_url')
        ]);

        $this->set('canShowInfo', true);
        $this->set('Form', $FormElement);
    }

    public function submit()
    {
        //User was assigned to this view by Login controller
        $this->setContainer('Logged as: {User.username}. <br><br>Redirecting to {redirect_url}');
    }
    
    public function submitOnError()
    {
        $this->setContainer('Invalid username or password.<br /><br /><small><u>Forgot your password?</u></small>');
    }

}
