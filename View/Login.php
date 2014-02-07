<?php
namespace Everon\View;

use Everon\Interfaces\DependencyContainer;
use Everon\View;
use Everon\Dependency;
use Everon\View\Index as DefaultView;

class Login extends DefaultView
{
    use Dependency\Injection\Response;
    
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
        $this->set('View.body', 'Logged as: <b>{User.username}</b><br/><br/> Redirecting to <i>{View.redirect_url}</i>');
        
        $url = $this->url($this->get('View.redirect_url'));
        $this->getResponse()->addHeader('refresh', '3; url='.$url);
    }
    
    public function submitOnError()
    {
        $this->set('View.body', 'Invalid username or password.<br /><br /><small><u>Forgot your password?</u></small>');
    }

}
