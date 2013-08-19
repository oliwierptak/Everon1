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

try {
    /**
     * @var Interfaces\Factory $Factory
     * @var Interfaces\Core $Console
     */
    $lib_dir = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'Src', 'Everon', 'Lib']).DIRECTORY_SEPARATOR;
    require_once($lib_dir.'Bootstrap.php');
    require_once($lib_dir.'Dependencies.php');
    
    $Console = $Factory->buildConsole();
    $Console->run();
}
catch (\Exception $e)
{
    echo "Uncaught exception: $e\n";
}