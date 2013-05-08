<?php
/*
 * Everon
 * (c) 2011-2013 Oliwier Ptak
 * */

namespace Everon
{
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);
    ini_set('xdebug.overload_var_dump', false);
    ini_set('html_errors', false);

    echo ('Start: '.memory_get_peak_usage(TRUE))."<hr/>";

    try {
        require_once(dirname(__FILE__) . '/../Src/Everon/Lib/Bootstrap.php');

        $Container = new Dependency\Container();
        $Factory = new Factory($Container);

        $Container->register('Logger', function() use ($Factory) {
            return $Factory->buildLogger(ev_DIR_LOG);
        });

        $Container->register('Request', function() use ($Factory) {
            return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            return $Factory->buildResponse();
        });

        $Container->register('ConfigExpressionMatcher', function() use ($Factory) {
            return $Factory->buildConfigExpressionMatcher();
        });

        $Matcher = $Container->resolve('ConfigExpressionMatcher');
        $Container->register('ConfigManager', function() use ($Factory, $Matcher) {
            return $Factory->buildConfigManager(
                $Matcher,
                ev_DIR_CONFIG, 
                ev_DIR_CACHE_CONFIG
            );
        });

        $Request = $Container->resolve('Request');
        $RouteConfig = $Container->resolve('ConfigManager')->getRouterConfig();
        $Container->register('Router', function() use ($Factory, $Request, $RouteConfig) {
            return $Factory->buildRouter(
                $Request,
                $RouteConfig
            );
        });

        $Container->register('Core', function() use ($Factory) {
            return $Factory->buildCore();
        });

        /**
         * @var \Everon\Config $Config
         */
        $Config = $Container->resolve('ConfigManager')->getApplicationConfig();
        $Container->register('ModelManager', function() use ($Factory, $Config) {
            return $Factory->buildModelManager($Config->go('model')->get('manager'));
        });

        /**
         * @var Core $Application
         */
        $Application = $Container->resolve('Core');
        register_shutdown_function(array($Application, 'shutdown'));
        
        $Application->start();        
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

    echo '<hr><pre>Executed in ', round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 's</pre>'.(memory_get_usage(TRUE))." / ".(memory_get_peak_usage(TRUE));
}


