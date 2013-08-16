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
     * @var Interfaces\DependencyContainer $Container
     * @var Interfaces\Factory $Factory
     * @var Interfaces\Core $Console
     */
    $lib_dir = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'Src', 'Everon', 'Lib']).DIRECTORY_SEPARATOR;
    require_once($lib_dir.'Bootstrap.php');
    require_once($lib_dir.'Dependencies.php');

    $Container->register('Request', function() use ($Factory) {
        mpr($_SERVER);
        die('fix me');
        return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
    });    
    
    $Console = $Factory->buildCoreConsole();
    $Console->run();
}
catch (\Exception $e)
{
    echo 'Uncaught exception</h1>';
    echo $e."\n";
    echo str_repeat('-', strlen($e))."\n";
    if (method_exists($e, 'getTraceAsString')) {
        echo $e->getTraceAsString();
    }
}

