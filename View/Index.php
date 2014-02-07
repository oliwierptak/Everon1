<?php
namespace Everon\View;

use Everon\View;
use Everon\Helper;

class Index extends View
{
    use Helper\Arrays;
    
    public function __construct($template_directory, array $vars)
    {
        $vars['AdminMenuItems'] = [];
        parent::__construct($template_directory, $vars);
    }
}
