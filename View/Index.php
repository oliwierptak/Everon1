<?php
namespace Everon\View;

use Everon\Interfaces;
use Everon\Helper;
use Everon\View;

class Index extends View
{
    use Helper\Arrays;
    
    public function __construct($template_directory, array $vars, Interfaces\Template $IndexContainer, $default_extension)
    {
        $vars['AdminMenuItems'] = [];
        parent::__construct($template_directory, $vars, $IndexContainer, $default_extension);
    }
}
