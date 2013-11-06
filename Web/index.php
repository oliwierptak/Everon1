<?php
/**
 * Everon application example.
 */
namespace Everon;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('xdebug.overload_var_dump', false);
ini_set('html_errors', false);

echo ('Start: '.memory_get_usage(TRUE)/1024)." kb<hr/>";


/**
 * @var Interfaces\DependencyContainer $Container
 * @var Interfaces\Factory $Factory
 * @var Interfaces\Core $Mvc
 */

$BootstrapFile = new \SplFileInfo(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', 'Config', 'Bootstrap', 'mvc.php'])
);
require_once($BootstrapFile);

$Application = $Factory->buildMvc();

$Application->run($Guid);