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
    
    $Container->register('Router', function() use ($Factory) {
        $Request = $Factory->getDependencyContainer()->resolve('Request');
        $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getConfigByName('console');
        $RouterValidator = $Factory->buildRouterValidator();
        
        return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
    });    

    $Container->register('Request', function() use ($Factory) {
        $args = $_SERVER['argv'];
        array_shift($args);
        $uri = array_shift($args);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SERVER_NAME'] = '';
        $_SERVER['QUERY_STRING'] = '';
        return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
    });    
    
    $Console = $Factory->buildConsole();
    $Console->run();
}
catch (\Exception $e)
{
    echo "\n$e\n";
}