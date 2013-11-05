<?php
namespace Everon\Console\Controller;

use Everon\Console\Controller;
use Everon\Interfaces;

class Help extends Controller implements Interfaces\Controller
{
    public function show()
    {
        $this->lines[] = "id: ".$this->getRequest()->getGetParameter('id', 0);
        $this->lines[] = "text: ".$this->getRequest()->getGetParameter('text', '');
    }
}