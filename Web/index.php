<?php
/**
 * Everon application example.
 */
namespace Everon;

error_reporting(E_ALL);
echo ('Start: '.memory_get_usage(TRUE)/1024)." kb<hr/>";

$BootstrapFile = new \SplFileInfo(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', 'Config', 'Bootstrap', 'mvc.php'])
);
require_once($BootstrapFile);

$Application = $Factory->buildMvc();
$Application->run($Guid);