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
require_once('/var/www/Everon/prepend.php');

$nesting = implode('..', array_fill(0, 4, DIRECTORY_SEPARATOR));
$root =  realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR;
require_once(implode(DIRECTORY_SEPARATOR, [$root, 'Src', 'Everon', 'Bootstrap.php']));
require_once(implode(DIRECTORY_SEPARATOR, [$root, 'Src', 'Everon', 'Guid.php']));

$Guid = new Guid();
Bootstrap::setup($Guid->getValue(), $root, '500.log');

$Environment = new Environment($root);
$Bootstrap = new Bootstrap($Environment);
list($Container, $Factory) = $Bootstrap->run();

$Container->propose('Environment', function() use ($Environment) {
    return $Environment;
});