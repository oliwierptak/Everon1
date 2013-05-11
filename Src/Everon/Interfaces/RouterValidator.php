<?php
namespace Everon\Interfaces;

use Everon\Interfaces;

interface RouterValidator
{
    function validate(Interfaces\ConfigItemRouter $RouteItem, Interfaces\Request $Request);
}