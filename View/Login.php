<?php
namespace Everon\View;

use Everon\View;

class Login extends View
{
    public function form()
    {
        $FormElement = new \Everon\View\Element\Form([
            'action' => $this->get('login_url')
        ]);
        
        $this->set('Form', $FormElement);
    }

    public function submit()
    {
        $this->setOutput('Logged as: {User.username}. <br><br>Redirecting to {redirect_url}');
    }
    
    public function submitOnError()
    {
        $this->setOutput('Invalid username or password.<br /><br /><small><u>Forgot your password?</u></small>');
    }

}
