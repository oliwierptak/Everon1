<?php
namespace Everon\View;

use Everon\View;

class Login extends View
{

    public function form()
    {
        $FormElement = new \Everon\View\Element\Form([
            'action' => 'login/submit/session/adf24ds34/redirect/account%5Csummary?and=something&else=2457'
        ]);
        
        $this->set('Form', $FormElement);
    }

    public function submit()
    {
        $this->setOutput('Logged as: {User.username}');
    }
    
    public function submitOnError()
    {
        $this->setOutput('Invalid username or password.<br /><br /><small><u>Forgot your password?</u></small>');
    }

}
