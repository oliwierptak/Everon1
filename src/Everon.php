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

/**
 * @var Guid $Guid
 * @var Interfaces\Factory $Factory
 * @var Interfaces\Core $Console
 */
require_once(
    implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', '..','..','..', 'Config', 'Bootstrap', 'console.php'])
);

$Console = $Factory->buildConsole();
$Console->run($Guid);