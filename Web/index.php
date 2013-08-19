<?php
/**
 * Everon application example.
 */
namespace Everon;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('xdebug.overload_var_dump', false);
ini_set('html_errors', false);

echo ('Start: '.memory_get_usage(TRUE)/1024)." kb<hr/>";

try {
    /**
     * @var Interfaces\DependencyContainer $Container
     * @var Interfaces\Factory $Factory
     * @var Interfaces\Core $Mvc
     */    
    $directory = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'Src', 'Everon', 'Lib']).DIRECTORY_SEPARATOR;
    require_once($directory.'Bootstrap.php');
    require_once($directory.'Dependencies.php');

    //replace default Router
    $Container->register('Router', function() use ($Factory) {
        $RouteConfig = $Factory->getDependencyContainer()->resolve('ConfigManager')->getRouterConfig();
        $RouterValidator = $Factory->buildRouterValidator();
        return $Factory->buildRouter($RouteConfig, $RouterValidator);
    });

    //replace default Request
    $Container->register('Request', function() use ($Factory) {
        return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
    });    
    
    $Application = $Factory->buildMvc();
    $Application->run();
}
catch (\Exception $e)
{
    echo '<pre><h1>500 Uncaught exception</h1>';
    echo $e."\n";
    echo '</pre>';
}

echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE)/1024)." / ".(memory_get_peak_usage(TRUE)/1024).' kb';


