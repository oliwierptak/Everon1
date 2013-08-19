<?php
namespace Everon\Controller\Console;

use Everon\Core\Console;
use Everon\Dependency;
use Everon\Interfaces;

class Help extends Console\Controller implements Interfaces\Controller
{
    public function show()
    {
        $this->lines[] = "id: ".$this->getRequest()->getGetParameter('id', 0);
        $this->lines[] = "text: ".$this->getRequest()->getGetParameter('text', '');
    }
}