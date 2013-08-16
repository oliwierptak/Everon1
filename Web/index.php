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
    $directory = implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'Src', 'Everon', 'Lib']).DIRECTORY_SEPARATOR;
    require_once($directory.'Bootstrap.php');
    require_once($directory.'Dependencies.php');
    /**
     * @var Interfaces\DependencyContainer $Container
     * @var Interfaces\Factory $Factory
     * @var Interfaces\Core $Application
     */    
    $Application = $Factory->buildCoreMvc();
    $Application->run();
}
catch (\Exception $e)
{
    echo '<pre><h1>500 Uncaught exception</h1>';
    echo $e."\n";
    echo str_repeat('-', strlen($e))."\n";
    if (method_exists($e, 'getTraceAsString')) {
        echo $e->getTraceAsString();
    }        
    echo '</pre>';
}

echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE)/1024)." / ".(memory_get_peak_usage(TRUE)/1024).' kb';


