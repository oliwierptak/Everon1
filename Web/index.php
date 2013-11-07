<?php
/**
 * Everon application example.
 */
namespace Everon;

require_once('/var/www/Kint/Kint.class.php');
error_reporting(E_ALL);
echo ('Start: '.memory_get_usage(TRUE)/1024)." kb<hr/>";

/**
 * @var Guid $Guid
 * @var Interfaces\Factory $Factory
 * @var Interfaces\Core $Application
 */
require_once( 
    (new \SplFileInfo(implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', 'Config', 'Bootstrap', 'mvc.php'])
)));

$Application = $Factory->buildMvc();
$Application->run($Guid);