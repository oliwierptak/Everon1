<?php
namespace Everon\Controller\Console;

use Everon\Core\Console;
use Everon\Dependency;
use Everon\Interfaces;

class Help extends Console\Controller implements Interfaces\Controller
{
    public function show()
    {
        $this->lines[] = "aaa";
    }

}