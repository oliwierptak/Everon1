<?php

namespace Everon\Mvc\Controller;

use Everon\Mvc\Controller;
use Everon\Dependency;
use Everon\Interfaces;

class Error extends Controller implements Interfaces\Controller
{
    /**
     * @inheritdoc
     */
    public function showException(\Exception $Exception, $code=400)
    {
        $this->getView()->show();
        $ViewManager = $this->getViewManager();
        $View = $ViewManager->getView('Index');
        $View->set('View.title', 'Error');
        $View->set('View.body', '');
        parent::showException($Exception, $code);
        
    }
}