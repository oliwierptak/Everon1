<?php
namespace Everon;

$CustomSetup = function(){}; //disable default exception handler

require_once(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', '..', 'Config', 'Bootstrap', 'mvc.php'])  
);