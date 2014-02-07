<?php
namespace Everon\View;

use Everon\View;
use Everon\View\Index as DefaultView;

class Error extends DefaultView
{
    public function show()
    {
        $this->set('title', 'Error');
    }
}
