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

require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Interfaces'.DIRECTORY_SEPARATOR.'Bootstrap.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Bootstrap.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'RequestIdentifier.php']));

//include the environment type (dev, staging, production), eg, const EVERON_ENVIRONMENT = 'dev';
@include_once($EVERON_ROOT.'env.php');
if (isset($EVERON_REQUEST_IDENTIFIER) === false) {
    $EVERON_REQUEST_IDENTIFIER = new RequestIdentifier();
}

if (isset($CUSTOM_EXCEPTION_HANDLER)) {
    $CUSTOM_EXCEPTION_HANDLER();
}
else {
    Bootstrap::setupExceptionHandler($EVERON_REQUEST_IDENTIFIER->getValue(), $EVERON_ROOT, 'error.log');
}

require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Interfaces', 'Environment.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$EVERON_SOURCE_ROOT, 'Environment.php']));

@$EVERON_CUSTOM_PATHS = $EVERON_CUSTOM_PATHS ?: [];

$EVERON_ENVIRONMENT = new Environment($EVERON_ROOT, $EVERON_SOURCE_ROOT, EVERON_ENVIRONMENT, $EVERON_CUSTOM_PATHS);
$EVERON_BOOTSTRAP = new Bootstrap($EVERON_ENVIRONMENT, EVERON_ENVIRONMENT);
$EVERON_FACTORY = $EVERON_BOOTSTRAP->run();

$EVERON_FACTORY->getDependencyContainer()->propose('Bootstrap', function() use ($EVERON_BOOTSTRAP) {
    return $EVERON_BOOTSTRAP;
});

$EVERON_FACTORY->getDependencyContainer()->propose('RequestIdentifier', function() use ($EVERON_REQUEST_IDENTIFIER) {
    return $EVERON_REQUEST_IDENTIFIER;
});

require_once($EVERON_ENVIRONMENT->getEveronConfig().'_dependencies.php');
