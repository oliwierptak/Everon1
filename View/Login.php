<?php
namespace Everon\View;

use Everon\View;

class Login extends View
{
    public function form()
    {
        $FormElement = new \Everon\View\Html\Form([
            'action' => $this->get('form_action_url') //get value set in Controller->form()
        ]);

        if ($this->get('canShowInfo')) {
            $this->set('View.reload', 'res234324fsfas23dfas'); //View.xxx variables will be accessible between partials
        }
        
        $this->set('canShowInfo', false); //view overpowers controller. canShowInfo will be set to false
        $this->set('Form', $FormElement); //variable accessible only in Login view
    }

    public function submit()
    {
        //User was assigned to this view by Login controller
        $this->set('View.Body', '<h3>Logged as: {User.username}</h3>. <br><br>Redirecting to {redirect_url}');
    }
    
    public function submitOnError()
    {
        $this->set('View.Body', 'Invalid username or password.<br /><br /><small><u>Forgot your password?</u></small>');
    }

}
