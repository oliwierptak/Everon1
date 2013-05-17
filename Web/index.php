<?php
/**
 * Everon application example.
 */

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
        $Environment = $Factory->buildEnvironment(PROJECT_ROOT);

        $log_directory = $Environment->getLog();
        $Container->register('Logger', function() use ($Factory, $log_directory) {
            return $Factory->buildLogger($log_directory);
        });

        $Container->register('Request', function() use ($Factory) {
            return $Factory->buildRequest($_SERVER, $_GET, $_POST, $_FILES);
        });

        $Container->register('Response', function() use ($Factory) {
            return $Factory->buildResponse();
        });

        $config_directory = $Environment->getConfig();
        $config_cache_directory = $Environment->getCacheConfig();        
        $Container->register('ConfigManager', function() use ($Factory, $config_directory, $config_cache_directory) {
            $Matcher = $Factory->buildConfigExpressionMatcher();
            $Loader = $Factory->buildConfigLoader($config_directory, $config_cache_directory);
            return $Factory->buildConfigManager($Loader, $Matcher);
        });

        $Request = $Container->resolve('Request');
        $RouteConfig = $Container->resolve('ConfigManager')->getRouterConfig();
        $Container->register('Router', function() use ($Factory, $Request, $RouteConfig) {
            $RouterValidator = $Factory->buildRouterValidator();
            return $Factory->buildRouter($Request, $RouteConfig, $RouterValidator);
        });

        $manager = $Container->resolve('ConfigManager')->getApplicationConfig()->go('model')->get('manager', 'Everon');
        $Container->register('ModelManager', function() use ($Factory, $manager) {
            return $Factory->buildModelManager($manager);
        });

        /**
         * @var Interfaces\Core $Application
         */
        $Application = $Factory->buildCore();
        register_shutdown_function(array($Application, 'shutdown'));

        $Router = $Container->resolve('Router');
        $ModelManager = $Container->resolve('ModelManager');
        
        $directory_view_template = $Environment->getViewTemplate();
        $compilers = $Container->resolve('ConfigManager')->getApplicationConfig()->go('view')->get('compilers');
        $view_manager = $Container->resolve('ConfigManager')->getApplicationConfig()->go('view')->get('manager', 'Everon');

        $Igniter = function() use ($Factory, $Router, $ModelManager, $directory_view_template, $compilers, $view_manager) {
            $class_name = $Router->getCurrentRoute()->getController();
            $ViewManager = $Factory->buildViewManager(
                $view_manager,
                $compilers,
                $directory_view_template
            );

            return $Factory->buildController($class_name, $ViewManager, $ModelManager);
        };
        
        $Application->start($Igniter, $Container->resolve('Response'));
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


