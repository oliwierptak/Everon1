<?php
namespace Everon\Controller;

use Everon\Controller;
use Everon\Interfaces;

class Login extends Controller implements Interfaces\Controller
{

    /**
     * @var \Everon\Model\UserPeer
     */
    protected $UserModel = null;

    public function initModel()
    {
        $this->UserModel = $this->getModel('UserPeer');
    }

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
        
        $User = $this->getModel('User')->authenticate($username, $password);
        if ($User === null) {
            //throw new \Everon\Exception\Controller('absdf');
            return false;
            
        }
        $this->getView()->set('User', $User); 

        return true;
    }



}