<?php
/**
 * Everon application example.
 */
namespace Everon;

$system_memory = (string) (memory_get_usage(true));
error_reporting(E_ALL);

/**
 * @var Guid $Guid
 * @var Interfaces\Factory $Factory
 * @var Interfaces\Core $Application
 */
require_once( 
    (new \SplFileInfo(implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', 'Config', 'Bootstrap', 'mvc.php'])
)));

$Guid->setSystemMemoryAtStart($system_memory);
$Application = $Factory->buildMvc();
$Application->run($Guid);