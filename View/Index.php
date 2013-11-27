<?php
namespace Everon\View;

use Everon\View;

class Index extends View
{
    public function __construct($template_directory, array $vars)
    {
        $vars['AdminMenuItems'] = [];
        parent::__construct($template_directory, $vars);
    }
}
