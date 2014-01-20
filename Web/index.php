<?php
/**
 * Everon application example.
 */
namespace Everon;
require_once('/var/www/kint/Kint.class.php');

$system_memory = (string) (memory_get_usage(true));
error_reporting(E_ALL);

/**
 * @var Guid $Guid
 * @var Interfaces\Factory $Factory
 * @var Interfaces\Core $Application
 */
require_once(
    implode(DIRECTORY_SEPARATOR,
        [dirname(__FILE__), '..', 'Config', 'Bootstrap', 'mvc.php']));

$Guid->setSystemMemoryAtStart($system_memory);
$Application = $Factory->buildMvc();
$Application->run($Guid);