<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

require_once(implode(DIRECTORY_SEPARATOR, [$everon_source_root, 'Bootstrap.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$everon_source_root, 'Guid.php']));

$Guid = new Guid();
if (isset($CustomSetup)) {
    $CustomSetup();
}
else {
    Bootstrap::setup($Guid->getValue(), $everon_root, '500.log');
}

require_once(implode(DIRECTORY_SEPARATOR, [$everon_source_root, 'Interfaces', 'Environment.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$everon_source_root, 'Environment.php']));

$Environment = new Environment($everon_root, $everon_source_root);
$Bootstrap = new Bootstrap($Environment);
list($Container, $Factory) = $Bootstrap->run();

$Container->propose('Environment', function() use ($Environment) {
    return $Environment;
});

$Container->propose('Bootstrap', function() use ($Bootstrap) {
    return $Bootstrap;
});