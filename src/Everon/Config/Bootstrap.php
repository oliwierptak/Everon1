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

require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Bootstrap.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Guid.php']));

$Guid = new Guid();
if (isset($CustomSetup)) {
    $CustomSetup();
}
else {
    Bootstrap::setup($Guid->getValue(), $EVERON_ROOT, '500.log');
}

require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Interfaces', 'Environment.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Environment.php']));

$Environment = new Environment($EVERON_ROOT, $EVERON_SOURCE_ROOT);
$Bootstrap = new Bootstrap($Environment);
list($Container, $Factory) = $Bootstrap->run();

$Container->propose('Environment', function() use ($Environment) {
    return $Environment;
});

$Container->propose('Bootstrap', function() use ($Bootstrap) {
    return $Bootstrap;
});