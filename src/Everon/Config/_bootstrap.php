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
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'RequestIdentifier.php']));

if (isset($REQUEST_IDENTIFIER) === false) {
    $REQUEST_IDENTIFIER = new RequestIdentifier();
}

if (isset($CUSTOM_EXCEPTION_HANDLER)) {
    $CUSTOM_EXCEPTION_HANDLER();
}
else {
    Bootstrap::setupExceptionHandler($REQUEST_IDENTIFIER->getValue(), $EVERON_ROOT, '500.log');
}

require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Interfaces', 'Environment.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Environment.php']));

$Environment = new Environment($EVERON_ROOT, $EVERON_SOURCE_ROOT);
$Bootstrap = new Bootstrap($Environment);
$Factory = $Bootstrap->run();
$Container = $Factory->getDependencyContainer();

$Container->propose('Bootstrap', function() use ($Bootstrap) {
    return $Bootstrap;
});

$Container->propose('Environment', function() use ($Environment) {
    return $Environment;
});

$Container->propose('RequestIdentifier', function() use ($REQUEST_IDENTIFIER) {
    return $REQUEST_IDENTIFIER;
});

require_once($Environment->getEveronConfig().'_dependencies.php');
